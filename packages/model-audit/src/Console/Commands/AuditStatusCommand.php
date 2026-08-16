<?php

namespace Johannesclimacus\ModelAudit\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Johannesclimacus\ModelAudit\Contracts\AuditStatusProvider;

#[Signature('model-audit:status')]
#[Description('Display audit status')]
class AuditStatusCommand extends Command
{
    public function __construct(private AuditStatusProvider $provider)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $status = $this->provider->get();

        $this->components->info(
            __('model-audit::commands.status.title')
        );

        $this->table(
            [],
            [
                [
                    __('model-audit::commands.status.enabled'),
                    $this->formatEnabled($status->enabled),
                ],
                [
                    __('model-audit::commands.status.connection'),
                    $status->connectionName,
                ],
                [
                    __('model-audit::commands.status.entries_table'),
                    $status->entriesTable,
                ],
                [
                    __('model-audit::commands.status.chain_states_table'),
                    $status->chainStatesTable,
                ],
                [
                    __('model-audit::commands.status.entries_count'),
                    $status->entriesCount,
                ],
                [
                    __('model-audit::commands.status.chains_count'),
                    $status->chainsCount,
                ],
                [
                    __('model-audit::commands.status.last_entry'),
                    $status->lastEntryAt?->toIso8601String()
                        ?? __('model-audit::commands.status.never'),
                ],
            ],
            'box-double',
        );

        return self::SUCCESS;
    }

    private function formatEnabled(bool $enabled): string
    {
        $color = $enabled ? 'green' : 'red';

        $value = $enabled
            ? __('model-audit::commands.status.yes')
            : __('model-audit::commands.status.no');

        return "<fg={$color}>● {$value}</>";
    }
}
