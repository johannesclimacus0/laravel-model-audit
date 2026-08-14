<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('model-audit.connection')
            ?: config('database.default');

        Schema::connection($connection)
            ->create(
                config('model-audit.chain_state_table', 'audit_chain_states'),
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('subject_type');
                    $table->string('subject_id');

                    $table->char('last_hash', 64)->nullable();
                    $table->uuid('last_entry_uuid')->nullable();
                    $table->unsignedBigInteger('entries_count')->default(0);

                    $table->timestamps(6);

                    $table->unique(['subject_type', 'subject_id']);
                }
            );
    }

    public function down(): void
    {
        $connection = config('model-audit.connection')
            ?: config('database.default');

        Schema::connection($connection)
            ->dropIfExists(
                config(
                    'model-audit.chain_state_table',
                    'audit_chain_states'
                )
            );
    }
};
