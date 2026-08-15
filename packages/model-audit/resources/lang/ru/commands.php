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
];
