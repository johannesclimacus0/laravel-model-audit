# Laravel Model Audit

Учебный пакет для Laravel, который сохраняет историю изменений Eloquent-моделей.
## Возможности

* автоматический аудит создания, изменения, удаления и восстановления моделей;
* сохранение только изменённых полей;
* сохранение старых и новых значений
* сохранение IP-адреса, User-Agent и ID запроса
* фильтрация и маскирование данных
* SHA-256-цепочку для проверки истории изменений
* проверка целостности через Artisan-команды и веб-интерфейс
* Blade-интерфейс
* Feature- и Unit- тесты

## Требования

- PHP 8.3+
- Laravel 13

## Установка

Установите пакет через Composer:

```
composer require johannesclimacus/model-audit
```

Запустите миграции:

```
php artisan migrate
```

Опубликуйте конфигурации:

```
php artisan vendor:publish --tag=model-audit-config
```

Также можно опубликовать переводы и Blade-шаблоны:

```
php artisan vendor:publish --tag=model-audit-lang
php artisan vendor:publish --tag=model-audit-views
```

## Быстрый старт

Подключите трейт `Auditable` к Eloquent-модели:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Johannesclimacus\ModelAudit\Traits\Auditable;

class User extends Model
{
    use Auditable;
}
```

После этого изменения модели будут автоматически записываться в аудит.

Модель с подключённым трейтом, а также с заготовками фильтрации и маскирования можно создать с помощью генератора:

```
php artisan make:auditable-model Invoice --migration
```

## Выбор отслеживаемых атрибутов

Чтобы отслеживать только определённые атрибуты, задайте их в модели:

```php
protected array $auditInclude = [
    //
];
```

Или исключите отдельные атрибуты:

```
protected array $auditExclude = [
    //
];
```

## Маскирование конфиденциальных значений

Настройте стратегии маскирования в отслеживаемой модели:

```
protected array $auditMasks = [
    'email' => 'email',
    'phone' => 'last_four',
    'password' => 'redact',
];
```

| Стратегия | Пример |
| --- | --- |
| `email` | `te***@example.com` |
| `last_four` | `******1234` |
| `redact` | `********` |

## Пользовательские события

Используйте `AuditLogger` для записи пользовательских бизнес-событий.

```
use Johannesclimacus\ModelAudit\Contracts\AuditLogger;

$logger = app(AuditLogger::class);

$logger->record(
    subject: $invoice,
    event: 'invoice.approved',
    metadata: [
        'reason' => 'Manager approval',
    ],
);
```

## Авторизация

Доступ к интерфейсу аудита защищён правилом Gate `viewModelAudit`. Определите его в методе `boot()` класса `AppServiceProvider`:

```
use App\Models\User;
use Illuminate\Support\Facades\Gate;

Gate::define(
    'viewModelAudit',
    fn (User $user): bool => $user->is_admin,
);
```

## Tailwind

Интерфейс использует Tailwind CSS 4. Добавьте шаблоны пакета и поддержку тёмной темы в `resources/css/app.css`:

```
@import 'tailwindcss';

@custom-variant dark (&:where(.dark, .dark *));

@source '../../vendor/johannesclimacus/model-audit/resources/views/**/*.blade.php';
```

Затем соберите стили:

```
npm install
npm run build
```


## Веб-интерфейс

Стандартные маршруты:

```
GET /audit
GET /audit/{uuid}
GET /audit/subjects/{type}/{id}
```


## Проверка целостности

Для каждого объекта создаётся отдельная цепочка хешей. Каждая запись хранит свой хеш и хеш предыдущей записи.

Проверить один объект:

```
php artisan model-audit:verify 'App\Models\Users' 1
```

Проверить все цепочки:

```
php artisan model-audit:verify-all
```

Посмотреть состояние пакета или историю объекта:

```
php artisan model-audit:status
php artisan model-audit:show 'App\Models\Users' 1
```


## Замена сервисов

Свою реализацию сервиса можно зарегистрировать в service provider приложения:

```
use App\Audit\ServiceActorResolver;
use Johannesclimacus\ModelAudit\Contracts\ActorResolver;

$this->app->singleton(
    ActorResolver::class,
    ServiceActorResolver::class,
);
```

Таким же способом можно заменить resolver, filter, masker, hasher, canonicalizer, recorder, reader и verifier.
