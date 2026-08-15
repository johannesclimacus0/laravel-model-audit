<?php

namespace Local\ModelAudit\Console;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

#[Signature('make:auditable-model
     {name : The name of the model}
     {--f|force : Create class even if it already exists}
     {--m|migration : Create a migration file for the model}
')]
#[Description('Create a new auditable Eloquent model class')]
class MakeAuditableModelCommand extends GeneratorCommand
{
    protected $type = 'Auditable model';

    public function handle(): int
    {
        $result = parent::handle();

        if ($result === false) {
            return self::FAILURE;
        }

        if (!$this->option('migration')) {
            return self::SUCCESS;
        }

        $modelName = class_basename(str_replace('/', '\\', $this->argument('name')));
        $table = Str::snake(Str::pluralStudly($modelName));

        $migrationResult = $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);

        return $migrationResult === self::SUCCESS
            ? self::SUCCESS
            : self::FAILURE;
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/auditable-model.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Models';
    }
}
