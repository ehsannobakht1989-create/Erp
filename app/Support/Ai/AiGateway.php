<?php

declare(strict_types=1);

namespace App\Support\Ai;

use App\Support\Contracts\AiGatewayContract;
use App\Support\Contracts\AuditContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use Throwable;

/**
 * Factory (config), module and capability switches must all be on; default off.
 * Models only read through permission-checked tools and create pending proposals.
 */
final class AiGateway implements AiGatewayContract
{
    private const MAX_TOOL_ROUNDS = 3;

    /**
     * @param  list<string>  $enabledModules
     * @param  array<string, AiProvider>  $providers
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private readonly ModuleCatalog $catalog,
        private readonly array $enabledModules,
        private readonly Connection $db,
        private readonly CompanyContext $context,
        private readonly AiToolRegistry $tools,
        private readonly array $providers,
        private readonly array $config,
    ) {}

    public function invoke(int $companyId, int $userId, string $moduleId, string $capabilityId, array $input): array
    {
        if ($companyId !== $this->context->companyId() || $userId < 1) {
            throw new LogicException('AI calls are restricted to the active company and a user.');
        }
        $capability = $this->capability($moduleId, $capabilityId);
        if ($capability === null || ! $this->enabled($companyId, $moduleId, $capabilityId)) {
            return ['ok' => false, 'status' => 'disabled', 'output' => null, 'error' => 'هوش مصنوعی برای این قابلیت غیرفعال است.'];
        }
        $provider = $this->providers[$capability['provider']] ?? null;
        if ($provider === null) {
            return ['ok' => false, 'status' => 'provider_unavailable', 'output' => null, 'error' => 'سرویس هوش مصنوعی پیکربندی نشده است.'];
        }
        if (! $this->withinBudget($companyId)) {
            return ['ok' => false, 'status' => 'budget_exceeded', 'output' => null, 'error' => 'بودجه ماهانه هوش مصنوعی تمام شده یا تعریف نشده است.'];
        }
        $started = hrtime(true);
        $usageId = $this->db->transaction(function () use ($companyId, $userId, $moduleId, $capabilityId, $capability): int {
            $now = now();
            $id = $this->db->table('core_ai_usage')->insertGetId(['company_id' => $companyId, 'user_id' => $userId, 'module' => $moduleId, 'capability' => $capabilityId, 'provider' => $capability['provider'], 'status' => 'started', 'created_at' => $now, 'updated_at' => $now]);
            app(AuditContract::class)->record($companyId, $userId, 'core', 'core_ai_usage', 'ai_call_started', $id, null, ['module' => $moduleId, 'capability' => $capabilityId, 'provider' => $capability['provider']]);

            return $id;
        });
        $redact = array_merge($this->config['redact'] ?? [], $capability['redact'] ?? []);
        $usage = ['input_tokens' => 0, 'output_tokens' => 0];
        try {
            $toolNames = array_values(array_filter($capability['tools'] ?? [], fn (string $tool) => $this->tools->has($tool)));
            $request = [
                'module' => $moduleId, 'capability' => $capabilityId, 'input' => $this->redact($input, $redact),
                'tools' => array_map(fn (string $tool) => ['name' => $tool] + $this->tools->describe($tool), $toolNames),
                'tool_results' => [], 'max_tokens' => (int) ($capability['max_tokens_per_call'] ?? $this->config['max_tokens'] ?? 1024), 'request_id' => (string) Str::uuid(),
            ];
            for ($round = 0; ; $round++) {
                $result = $provider->complete($request);
                $usage['input_tokens'] += max(0, $result['usage']['input_tokens']);
                $usage['output_tokens'] += max(0, $result['usage']['output_tokens']);
                if ($result['tool_calls'] === []) {
                    break;
                }
                if ($round + 1 >= self::MAX_TOOL_ROUNDS) {
                    throw new LogicException('AI tool-call round limit exceeded.');
                }
                foreach ($result['tool_calls'] as $call) {
                    if (! in_array($call['name'], $toolNames, true)) {
                        throw new LogicException('Model requested an undeclared tool.');
                    }
                    $request['tool_results'][] = $call + ['result' => $this->redact(['value' => $this->tools->execute($call['name'], $companyId, $userId, $call['arguments'])], $redact)['value']];
                }
            }
            $proposalIds = $this->db->transaction(function () use ($result, $capability, $companyId, $userId, $usageId, $moduleId, $capabilityId, $usage, $started): array {
                $ids = [];
                foreach ($result['proposals'] as $proposal) {
                    if (! is_array($proposal) || ! in_array($proposal['type'] ?? null, $capability['proposals'] ?? [], true) || ! is_array($proposal['payload'] ?? null)) {
                        throw new LogicException('Model returned an undeclared or malformed proposal.');
                    }
                    $now = now();
                    $ids[] = $this->db->table('core_ai_proposals')->insertGetId(['company_id' => $companyId, 'user_id' => $userId, 'usage_id' => $usageId, 'module' => $moduleId, 'capability' => $capabilityId, 'type' => $proposal['type'], 'payload' => json_encode($proposal['payload'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), 'status' => 'pending', 'created_at' => $now, 'updated_at' => $now]);
                }
                $this->finish($usageId, $companyId, $userId, $capability['provider'], 'succeeded', $usage, $started, null);

                return $ids;
            });

            return ['ok' => true, 'status' => 'ok', 'output' => $result['output'], 'proposal_ids' => $proposalIds];
        } catch (Throwable $e) {
            report($e);
            $this->db->transaction(fn () => $this->finish($usageId, $companyId, $userId, $capability['provider'], 'failed', $usage, $started, mb_substr($e::class.': '.$e->getMessage(), 0, 1000)));

            return ['ok' => false, 'status' => 'failed', 'output' => null, 'error' => 'درخواست هوش مصنوعی ناموفق بود.'];
        }
    }

    public function enabled(int $companyId, string $moduleId, string $capabilityId): bool
    {
        $capability = $this->capability($moduleId, $capabilityId);
        if ($capability === null || ($this->config['enabled'] ?? false) !== true || ($this->config['kill_switch'] ?? false) === true || $capability['provider'] === 'off') {
            return false;
        }
        $toggles = $this->db->table('core_ai_toggles')->where(['company_id' => $companyId, 'module' => $moduleId])->whereIn('capability', ['', $capabilityId])->pluck('enabled', 'capability');
        $module = $toggles[''] ?? ($this->catalog->manifests()[$moduleId]['ai']['enabled_default'] ?? false);

        return (bool) $module && (bool) ($toggles[$capabilityId] ?? false);
    }

    public function setToggle(int $companyId, string $moduleId, ?string $capabilityId, bool $enabled, int $actorId): void
    {
        if ($companyId !== $this->context->companyId() || ($capabilityId !== null && $this->capability($moduleId, $capabilityId) === null) || ! isset($this->catalog->manifests()[$moduleId]['ai'])) {
            throw new InvalidArgumentException('AI toggle must target a declared capability in the active company.');
        }
        $this->db->transaction(function () use ($companyId, $moduleId, $capabilityId, $enabled, $actorId): void {
            $scope = ['company_id' => $companyId, 'module' => $moduleId, 'capability' => $capabilityId ?? ''];
            $before = $this->db->table('core_ai_toggles')->where($scope)->lockForUpdate()->value('enabled');
            $this->db->table('core_ai_toggles')->updateOrInsert($scope, ['enabled' => $enabled, 'updated_at' => now(), 'created_at' => now()]);
            app(AuditContract::class)->record($companyId, $actorId, 'core', 'core_ai_toggles', 'updated', $moduleId.':'.($capabilityId ?? '*'), $before === null ? null : ['enabled' => (bool) $before], ['enabled' => $enabled]);
        });
    }

    public function setMonthlyBudget(int $companyId, string $limit, int $actorId): void
    {
        if ($companyId !== $this->context->companyId() || preg_match('/^\d{1,16}(?:\.\d{1,4})?$/D', $limit) !== 1) {
            throw new InvalidArgumentException('Budget must be a non-negative decimal for the active company.');
        }
        $this->db->transaction(function () use ($companyId, $limit, $actorId): void {
            $before = $this->db->table('core_ai_budgets')->where('company_id', $companyId)->lockForUpdate()->value('monthly_limit');
            $this->db->table('core_ai_budgets')->updateOrInsert(['company_id' => $companyId], ['monthly_limit' => $limit, 'updated_at' => now(), 'created_at' => now()]);
            app(AuditContract::class)->record($companyId, $actorId, 'core', 'core_ai_budgets', 'updated', $companyId, $before === null ? null : ['monthly_limit' => (string) $before], ['monthly_limit' => $limit]);
        });
    }

    /** Accept/reject only records the decision; owning modules apply accepted proposals through their own services. */
    public function decideProposal(int $proposalId, bool $accept, int $actorId): void
    {
        $companyId = $this->context->companyId();
        $this->db->transaction(function () use ($proposalId, $accept, $actorId, $companyId): void {
            $proposal = $this->db->table('core_ai_proposals')->where(['id' => $proposalId, 'company_id' => $companyId])->lockForUpdate()->first();
            if ($proposal === null || $proposal->status !== 'pending') {
                throw new InvalidArgumentException('Pending proposal not found in the active company.');
            }
            $status = $accept ? 'accepted' : 'rejected';
            $this->db->table('core_ai_proposals')->where('id', $proposalId)->update(['status' => $status, 'decided_by' => $actorId, 'decided_at' => now(), 'updated_at' => now()]);
            app(AuditContract::class)->record($companyId, $actorId, 'core', 'core_ai_proposals', $status, $proposalId, ['status' => 'pending'], ['status' => $status]);
        });
    }

    /** @return array<string, mixed>|null */
    private function capability(string $moduleId, string $capabilityId): ?array
    {
        if (! in_array($moduleId, $this->enabledModules, true)) {
            return null;
        }
        foreach ($this->catalog->manifests()[$moduleId]['ai']['capabilities'] ?? [] as $capability) {
            if ($capability['id'] === $capabilityId) {
                return $capability;
            }
        }

        return null;
    }

    private function withinBudget(int $companyId): bool
    {
        $limit = $this->db->table('core_ai_budgets')->where('company_id', $companyId)->value('monthly_limit');
        if ($limit === null) {
            return false;
        }
        $spent = BigDecimal::zero();
        $monthStart = now()->utc()->startOfMonth();
        foreach ($this->db->table('core_ai_usage')->where('company_id', $companyId)->where('created_at', '>=', $monthStart)->pluck('cost') as $cost) {
            $spent = $spent->plus(BigDecimal::of((string) $cost));
        }

        return $spent->isLessThan(BigDecimal::of((string) $limit));
    }

    /** @param array{input_tokens:int, output_tokens:int} $usage */
    private function finish(int $usageId, int $companyId, int $userId, string $provider, string $status, array $usage, int|float $started, ?string $error): void
    {
        $prices = $this->config['prices'][$provider] ?? [];
        $cost = BigDecimal::of((string) ($prices['input_per_1k'] ?? '0'))->multipliedBy($usage['input_tokens'])
            ->plus(BigDecimal::of((string) ($prices['output_per_1k'] ?? '0'))->multipliedBy($usage['output_tokens']))
            ->dividedBy(1000, 4, RoundingMode::UP);
        $this->db->table('core_ai_usage')->where('id', $usageId)->update(['status' => $status, 'input_tokens' => $usage['input_tokens'], 'output_tokens' => $usage['output_tokens'], 'cost' => (string) $cost, 'duration_ms' => (int) ((hrtime(true) - $started) / 1_000_000), 'error' => $error, 'updated_at' => now()]);
        app(AuditContract::class)->record($companyId, $userId, 'core', 'core_ai_usage', 'ai_call_'.$status, $usageId, ['status' => 'started'], ['status' => $status, 'cost' => (string) $cost]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $fields
     * @return array<string, mixed>
     */
    private function redact(array $data, array $fields): array
    {
        $fields = array_map('mb_strtolower', $fields);
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(mb_strtolower($key), $fields, true)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->redact($value, $fields);
            }
        }

        return $data;
    }
}
