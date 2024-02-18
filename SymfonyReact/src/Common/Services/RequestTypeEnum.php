<?php

namespace App\Common\Services;

enum RequestTypeEnum: string
{
    case FULL = 'full';

    case ONLY = 'only';

    case SENSITIVE_FULL = 'sensitive-full';

    case SENSITIVE_ONLY = 'sensitive-only';
}
