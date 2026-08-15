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
];
