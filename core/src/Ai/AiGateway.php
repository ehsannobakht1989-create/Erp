<?php
declare(strict_types=1);

namespace Core\Ai;

use Core\Config;
use Core\Contracts\AiGatewayContract;
use Core\Contracts\PermissionContract;
use Core\Database\Connection;
use Core\Module\ModuleRegistry;

final class AiGateway implements AiGatewayContract
{
    /** @var array<string, callable> */
    private array $toolHandlers = [];

    public function __construct(
        private readonly ModuleRegistry $registry,
        private readonly PermissionContract $permissions,
        private readonly Config $config,
        private readonly ?Connection $db = null,
    ) {
    }

    public function registerTool(string $toolName, callable $handler): void
    {
        $this->toolHandlers[$toolName] = $handler;
    }

    public function invoke(
        int $companyId,
        int $userId,
        string $moduleId,
        string $capabilityId,
        array $input,
    ): array {
        if ($this->config->bool('ai.kill_switch', false) || !$this->config->bool('ai.enabled', false)) {
            return ['ok' => false, 'status' => 'disabled', 'output' => null, 'error' => 'AI disabled'];
        }

        $manifest = $this->registry->get($moduleId);
        if ($manifest === null || empty($manifest['ai']['enabled_default'])) {
            return ['ok' => false, 'status' => 'disabled', 'output' => null, 'error' => 'AI disabled for module'];
        }

        $cap = null;
        foreach ($manifest['ai']['capabilities'] ?? [] as $c) {
            if (($c['id'] ?? '') === $capabilityId) {
                $cap = $c;
                break;
            }
        }

        $provider = $cap['provider'] ?? 'off';
        if ($cap === null || $provider === 'off') {
            return ['ok' => false, 'status' => 'disabled', 'output' => null, 'error' => 'AI disabled'];
        }

        // Redact sensitive fields before leaving server
        $redactFields = $cap['redact'] ?? [];
        $sanitized = $this->redact($input, $redactFields);

        // Execute requested tool if any, enforcing current user's permissions
        $toolCalls = [];
        if (isset($sanitized['tool']) && is_string($sanitized['tool'])) {
            $toolName = $sanitized['tool'];
            $allowedTools = $cap['tools'] ?? [];
            if (!in_array($toolName, $allowedTools, true)) {
                return ['ok' => false, 'status' => 'forbidden', 'output' => null, 'error' => "Tool '$toolName' not declared in manifest"];
            }
            $perm = $moduleId . '.ai.use';
            if (!$this->permissions->can($userId, $companyId, $perm)) {
                return ['ok' => false, 'status' => 'forbidden', 'output' => null, 'error' => 'Insufficient permissions for AI tool'];
            }
            if (isset($this->toolHandlers[$toolName])) {
                $toolCalls[] = $toolName;
                $sanitized['tool_result'] = ($this->toolHandlers[$toolName])($sanitized['tool_args'] ?? [], $companyId, $userId);
            }
        }

        // Proposal creation (AI never writes directly)
        $proposalId = null;
        if (isset($sanitized['propose']) && is_array($sanitized['propose']) && $this->db !== null) {
            $this->db->execute(
                'INSERT INTO ai__proposals (company_id, user_id, module_id, capability, proposal_type, payload, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $companyId,
                    $userId,
                    $moduleId,
                    $capabilityId,
                    (string) ($sanitized['propose']['type'] ?? 'generic'),
                    (string) json_encode($sanitized['propose']['payload'] ?? [], JSON_UNESCAPED_UNICODE),
                    'pending',
                    gmdate('Y-m-d H:i:s'),
                ]
            );
            $proposalId = (int) $this->db->lastInsertId();
        }

        $output = match ($provider) {
            'avalai' => $this->callAvalai($sanitized, (int) ($cap['max_tokens_per_call'] ?? 2000)),
            'python' => $this->callPython($capabilityId, $sanitized),
            default => null,
        };

        // Audit usage
        if ($this->db !== null) {
            $this->db->execute(
                'INSERT INTO ai__usage (company_id, user_id, module_id, capability, provider, prompt_hash, tool_calls, tokens_in, tokens_out, estimated_cost, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $companyId,
                    $userId,
                    $moduleId,
                    $capabilityId,
                    $provider,
                    hash('sha256', (string) json_encode($sanitized)),
                    (string) json_encode($toolCalls),
                    0,
                    0,
                    '0.0000',
                    gmdate('Y-m-d H:i:s'),
                ]
            );
        }

        $res = ['ok' => true, 'status' => 'ok', 'output' => $output];
        if ($proposalId !== null) {
            $res['proposal_id'] = $proposalId;
        }
        return $res;
    }

    /** @param array<string, mixed> $data @param array<int, string> $fields @return array<string, mixed> */
    private function redact(array $data, array $fields): array
    {
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $data[$f] = '[REDACTED]';
            }
        }
        return $data;
    }

    /** @param array<string, mixed> $payload */
    private function callAvalai(array $payload, int $maxTokens): mixed
    {
        $apiKey = $this->config->string('ai.avalai_api_key', '');
        if ($apiKey === '') {
            return ['message' => 'AvalAI API key not configured'];
        }
        $baseUrl = rtrim($this->config->string('ai.avalai_base_url', 'https://api.avalai.ir/v1'), '/');
        $ch = curl_init($baseUrl . '/chat/completions');
        if ($ch === false) {
            return null;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => (string) json_encode([
                'model' => 'gpt-4o-mini',
                'max_tokens' => $maxTokens,
                'messages' => $payload['messages'] ?? [['role' => 'user', 'content' => (string) ($payload['prompt'] ?? '')]],
            ]),
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        return is_string($raw) ? json_decode($raw, true) : null;
    }

    /** @param array<string, mixed> $payload */
    private function callPython(string $capabilityId, array $payload): mixed
    {
        $baseUrl = rtrim($this->config->string('ai.service_url', 'http://127.0.0.1:8000'), '/');
        $endpoint = match ($capabilityId) {
            'forecast' => '/v1/forecast',
            'anomaly' => '/v1/anomaly',
            'ocr' => '/v1/ocr',
            default => '/v1/optimize',
        };
        $ch = curl_init($baseUrl . $endpoint);
        if ($ch === false) {
            return null;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => (string) json_encode($payload),
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
        return is_string($raw) ? json_decode($raw, true) : null;
    }
}
