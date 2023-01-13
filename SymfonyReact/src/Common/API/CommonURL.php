<?php

namespace App\Common\API;

class CommonURL
{
    private const MAIN_BASE_URL = '/HomeApp/';

    public const USER_HOMEAPP_API_URL = self::MAIN_BASE_URL . 'api/user/';

    public const HOMEAPP_WEBAPP_URL_BASE = self::MAIN_BASE_URL . 'WebApp/';

    public const DEVICE_HOMEAPP_API_URL = 'api/device/';
}
