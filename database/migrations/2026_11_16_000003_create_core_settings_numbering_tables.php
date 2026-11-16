<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('module', 64);
            $table->string('key', 128);
            $table->text('value');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'module', 'key']);
        });
        Schema::create('core_number_formats', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('sequence', 128);
            $table->string('pattern', 100);
            $table->timestamps();
            $table->unique(['company_id', 'sequence']);
        });
        Schema::create('core_number_sequences', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('sequence', 128);
            $table->unsignedBigInteger('branch_id')->default(0);
            $table->unsignedBigInteger('fiscal_year_id')->default(0);
            $table->string('period', 16)->default('');
            $table->unsignedBigInteger('next_value')->default(1);
            $table->timestamps();
            $table->unique(['company_id', 'sequence', 'branch_id', 'fiscal_year_id', 'period'], 'core_number_sequences_scope_unique');
        });
    }

    public function down(): void
    {
        foreach (['core_number_sequences', 'core_number_formats', 'core_settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
