<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('model-audit.connection') ?: config('database.default');
        Schema::connection($connection)->table(
            config('model-audit.table', 'audit_entries'),
            function (Blueprint $table): void {
                $table->char('previous_hash', 64)->nullable();
                $table->char('hash', 64)->nullable();
            }
        );
    }

    public function down(): void
    {
        $connection = config('model-audit.connection')
            ?: config('database.default');

        Schema::connection($connection)->table(
            config('model-audit.table', 'audit_entries'),
            function (Blueprint $table): void {
                $table->dropColumn([
                    'previous_hash',
                    'hash',
                ]);
            }
        );
    }
};
