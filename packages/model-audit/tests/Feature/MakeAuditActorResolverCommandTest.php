<?php

namespace Local\ModelAudit\Tests\Feature;

use Local\ModelAudit\Tests\GeneratorTestCase;

class MakeAuditActorResolverCommandTest extends GeneratorTestCase
{
    public function test_it_creates_an_actor_resolver(): void
    {
        $this->assertGeneratorCreates(
            command: 'make:audit-actor-resolver',
            name: 'InvoiceActorResolver',
            relativePath: '/ModelAudit/Resolvers/InvoiceActorResolver.php',
            expectedContents: [
                'class InvoiceActorResolver implements ActorResolver',
                'public function resolve(): ?Model',
                'return null;',
            ],
        );
    }
}
