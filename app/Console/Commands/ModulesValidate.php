<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Modules\ManifestValidator;
use Illuminate\Console\Command;

final class ModulesValidate extends Command
{
    protected $signature = 'erp:modules:validate';

    protected $description = 'Validate manifests against the supported schema and dependency/naming rules';

    public function handle(ManifestValidator $validator): int
    {
        $errors = $validator->validate(base_path('modules'), base_path('docs/module.schema.json'));
        foreach ($errors as $error) {
            $this->error($error);
        }
        if ($errors !== []) {
            return self::FAILURE;
        }
        $this->info('All module manifests are valid.');

        return self::SUCCESS;
    }
}
