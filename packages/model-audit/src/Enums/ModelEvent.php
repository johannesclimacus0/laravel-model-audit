<?php

namespace Local\ModelAudit\Enums;

enum ModelEvent: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
}
