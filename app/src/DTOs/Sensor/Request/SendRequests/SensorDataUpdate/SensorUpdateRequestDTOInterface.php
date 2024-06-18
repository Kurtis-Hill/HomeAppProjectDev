<?php

namespace App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate;

interface SensorUpdateRequestDTOInterface
{
    public function getPinNumber(): int;

    public function getReadingInterval(): int;

    public function getSensorName(): string;
}
