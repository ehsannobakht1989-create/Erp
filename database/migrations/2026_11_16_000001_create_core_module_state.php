<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_modules', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->boolean('enabled');
        });
        Schema::create('core_module_locks', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
        });
        DB::table('core_module_locks')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('core_module_locks');
        Schema::dropIfExists('core_modules');
    }
};
