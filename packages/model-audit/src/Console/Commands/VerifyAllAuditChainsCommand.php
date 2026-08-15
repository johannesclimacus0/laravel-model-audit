<?php

namespace Local\ModelAudit\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Local\ModelAudit\Contracts\AuditChainFinder;
use Local\ModelAudit\Contracts\AuditChainVerifier;

#[Signature('model-audit:verify-all')]
#[Description('Verify all audit chains')]
class VerifyAllAuditChainsCommand extends Command
{
    public function __construct(
        private AuditChainFinder $finder,
        private AuditChainVerifier $verifier
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $verifiedCount = 0;
        $invalidCount = 0;

        foreach ($this->finder->all() as $identifier) {
            $verifiedCount++;

            $result = $this->verifier->verify(
                $identifier->subjectType,
                $identifier->subjectId,
            );

            if ($result->valid) {
                continue;
            }

            $invalidCount++;

            $this->error(
                __('model-audit::commands.verify_all.invalid_chain', [
                    'subject_type' => $identifier->subjectType,
                    'subject_id' => $identifier->subjectId,
                ])
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
        }

        if ($verifiedCount === 0) {
            $this->info(
                __('model-audit::commands.verify_all.none')
            );

            return self::SUCCESS;
        }

        $this->line(
            __('model-audit::commands.verify_all.verified', [
                'count' => $verifiedCount,
            ])
        );

        if ($invalidCount > 0) {
            $this->error(
                __('model-audit::commands.verify_all.invalid_count', [
                    'count' => $invalidCount,
                ])
            );

            return self::FAILURE;
        }

        $this->info(
            __('model-audit::commands.verify_all.valid')
        );

        return self::SUCCESS;
    }
}
