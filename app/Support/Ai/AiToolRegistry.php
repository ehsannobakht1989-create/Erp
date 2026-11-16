<?php

declare(strict_types=1);

namespace App\Support\Ai;

use App\Support\Contracts\PermissionContract;
use App\Support\Modules\ModuleCatalog;
use InvalidArgumentException;

final class AiToolRegistry
{
    /** @var array<string, array{permission:string, handler:class-string<AiTool>}> */
    private array $tools = [];

    public function __construct(private readonly ModuleCatalog $catalog) {}

    /** @param class-string<AiTool> $handler */
    public function register(string $tool, string $permission, string $handler): void
    {
        $module = explode('.', $tool, 2)[0];
        $declared = [];
        foreach ($this->catalog->manifests()[$module]['ai']['capabilities'] ?? [] as $capability) {
            $declared = array_merge($declared, $capability['tools'] ?? []);
        }
        if (! in_array($tool, $declared, true) || isset($this->tools[$tool]) || ! in_array($permission, $this->catalog->manifests()[$module]['permissions'] ?? [], true)) {
            throw new InvalidArgumentException('AI tool and permission must be declared once by the owning manifest: '.$tool);
        }
        if (! is_subclass_of($handler, AiTool::class)) {
            throw new InvalidArgumentException('AI tool handler must implement AiTool.');
        }
        $this->tools[$tool] = ['permission' => $permission, 'handler' => $handler];
    }

    public function has(string $tool): bool
    {
        return isset($this->tools[$tool]);
    }

    /** @return array{description:string, parameters:array<string,mixed>} */
    public function describe(string $tool): array
    {
        return app($this->tool($tool)['handler'])->describe();
    }

    /** @param array<string, mixed> $arguments */
    public function execute(string $tool, int $companyId, int $userId, array $arguments): mixed
    {
        $definition = $this->tool($tool);
        if (! app(PermissionContract::class)->can($userId, $companyId, $definition['permission'])) {
            return ['error' => 'forbidden'];
        }

        return app($definition['handler'])->run($companyId, $userId, $arguments);
    }

    /** @return array{permission:string, handler:class-string<AiTool>} */
    private function tool(string $tool): array
    {
        return $this->tools[$tool] ?? throw new InvalidArgumentException('Unknown AI tool: '.$tool);
    }
}
