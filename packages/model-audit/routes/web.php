<?php

use Illuminate\Support\Facades\Route;
use Local\ModelAudit\Http\Controllers\AuditController;
use Local\ModelAudit\Http\Middleware\AuthorizeModelAudit;

$middleware = array_merge(
    (array) config('model-audit.ui.middleware', ['web']),
    [AuthorizeModelAudit::class],
);

$prefix = trim(
    (string) config('model-audit.ui.prefix', 'audit'), '/'
);
$namePrefix = rtrim(
    (string) config('model-audit.ui.route_name_prefix',
        'model-audit.'), '.'
    ) . '.';

Route::middleware($middleware)
    ->prefix($prefix)
    ->name($namePrefix)
    ->group(function (): void {
        Route::get('/', [AuditController::class, 'index'])
            ->name('index');

        Route::get('/subjects/{type}/{id}', [AuditController::class, 'subject'])
            ->name('subject');

        Route::get('/{audit:uuid}', [AuditController::class, 'show'])
            ->whereUuid('audit')
            ->name('show');
    });
