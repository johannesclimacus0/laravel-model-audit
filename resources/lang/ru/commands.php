<?php

return [
    'verify' => [
        'valid' => 'Цепочка аудита не повреждена.',
        'invalid' => 'Цепочка аудита повреждена.',
        'reason' => 'Причина: :reason',
        'entry_uuid' => 'UUID записи: :uuid',
    ],
    'verify_all' => [
        'none' => 'Цепочки аудита не найдены.',
        'valid' => 'Все цепочки аудита не повреждены.',
        'invalid_chain' => 'Повреждённая цепочка аудита: :subject_type [:subject_id]',
        'verified' => 'Проверено цепочек: :count',
        'invalid_count' => 'Повреждено цепочек: :count',
    ],
    'status' => [
        'title' => 'Статус аудита',
        'enabled' => 'Включен',
        'connection' => 'Подключение',
        'entries_table' => 'Таблица записей',
        'chain_states_table' => 'Таблица состояний цепочек',
        'entries_count' => 'Записей аудита',
        'chains_count' => 'Цепочек',
        'last_entry' => 'Последняя запись',
        'yes' => 'Да',
        'no' => 'Нет',
        'never' => 'отсутствует',
    ],
    'show' => [
        'title' => 'История аудита: :subject_type [:subject_id]',
        'none' => 'Записи аудита не найдены.',
        'system' => 'система',
        'invalid_limit_integer' => 'Лимит должен быть целым числом.',
        'invalid_limit_range' => 'Лимит должен быть от 1 до :max.',
        'subject_type_required' => 'Тип субъекта не может быть пустым.',
        'subject_id_required' => 'ID субъекта не может быть пустым.',
        'json_encoding_failed' => 'Не удалось преобразовать историю аудита в JSON.',
        'headers' => [
            'uuid' => 'UUID',
            'event' => 'Событие',
            'actor' => 'Инициатор',
            'created_at' => 'Создано',
        ],
    ],
];
