<?php

namespace Local\ModelAudit\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Local\ModelAudit\Contracts\AuditChainVerifier;

#[Signature('model-audit:verify {subjectType : Stored morph type} {subjectId : Subject primary key}')]
#[Description('Verify the audit chain for subject')]
class VerifyAuditChainCommand extends Command
{
    public function __construct(private AuditChainVerifier $verifier)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $subjectType = (string) $this->argument('subjectType');
        $subjectId = (string) $this->argument('subjectId');

        $result = $this->verifier->verify($subjectType, $subjectId);

        if ($result->valid) {
            $this->info(
                __('model-audit::commands.verify.valid')
            );

            return self::SUCCESS;
        }

        $this->error(
            __('model-audit::commands.verify.invalid')
        );

        if ($result->failure !== null) {
            $this->line(
                __('model-audit::commands.verify.reason', [
                    'reason' => $result->failure->value,
                ])
            );
        }

        if ($result->failedEntryUuid !== null) {
            $this->line(
                __('model-audit::commands.verify.entry_uuid', [
                    'uuid' => $result->failedEntryUuid,
                ])
            );
        }

        return self::FAILURE;
    }
}
