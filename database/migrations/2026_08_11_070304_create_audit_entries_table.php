<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('model-audit.connection') ?: config('database.default');

        Schema::connection($connection)->create(config('model-audit.table', 'audit_entries'),
            function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('subject_type');
                $table->string('subject_id');
                $table->string('actor_type')->nullable();
                $table->string('actor_id')->nullable();
                $table->string('event');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('request_id')->nullable();
                $table->dateTime('created_at', 6);

                $table->index(['subject_type', 'subject_id']);
                $table->index(['actor_type', 'actor_id']);
                $table->index('event');
                $table->index('request_id');
                $table->index('created_at');
            });
    }

    public function down(): void
    {
        $connection = config('model-audit.connection') ?: config('database.default');

        Schema::connection($connection)->dropIfExists(config('model-audit.table', 'audit_entries'));
    }
};
