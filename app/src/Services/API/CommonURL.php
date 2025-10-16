<?php

namespace App\Services\API;

class CommonURL
{
    public const MAIN_BASE_URL = '/';

    public const APT_V1 = 'api/';

    public const USER_BASE_API_URL = self::MAIN_BASE_URL . self::APT_V1;

    public const USER_HOMEAPP_API_URL = self::USER_BASE_API_URL . 'user/';

    public const HOMEAPP_WEBAPP_URL_BASE = self::MAIN_BASE_URL . 'WebApp/';

    public const DEVICE_HOMEAPP_API_URL = self::MAIN_BASE_URL . self::APT_V1 . 'device/';
}
