<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

interface SensorUpdateRequestDTOInterface
{
    public function getPinNumber(): int;

    public function getReadingInterval(): int;
}
