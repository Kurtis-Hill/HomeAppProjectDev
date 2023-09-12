<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

interface SensorUpdateRequestDTOInterface
{
    public function getPinNumber(): string|int;

    public function getReadingInterval(): int;

    public function getSensorName(): string;
}
