<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_users', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 190);
            $table->string('email', 190)->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->string('locale', 8)->default('fa');
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
        Schema::create('core_personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('core_jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        Schema::create('core_failed_jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['core_failed_jobs', 'core_jobs', 'core_personal_access_tokens', 'core_users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
