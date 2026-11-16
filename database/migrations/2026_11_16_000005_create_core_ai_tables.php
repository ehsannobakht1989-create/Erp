<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_ai_toggles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('module', 64);
            $table->string('capability', 32)->default('');
            $table->boolean('enabled');
            $table->timestamps();
            $table->unique(['company_id', 'module', 'capability']);
        });
        Schema::create('core_ai_budgets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id')->unique();
            $table->decimal('monthly_limit', 20, 4);
            $table->timestamps();
        });
        Schema::create('core_ai_usage', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('module', 64);
            $table->string('capability', 32);
            $table->string('provider', 16);
            $table->string('status', 24);
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('cost', 20, 4)->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });
        Schema::create('core_ai_proposals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('usage_id');
            $table->string('module', 64);
            $table->string('capability', 32);
            $table->string('type', 128);
            $table->longText('payload');
            $table->string('status', 16)->default('pending');
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        foreach (['core_ai_proposals', 'core_ai_usage', 'core_ai_budgets', 'core_ai_toggles'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
