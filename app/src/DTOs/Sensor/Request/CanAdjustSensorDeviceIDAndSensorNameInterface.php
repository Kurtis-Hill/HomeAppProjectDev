<?php

namespace App\DTOs\Sensor\Request;

interface CanAdjustSensorDeviceIDAndSensorNameInterface
{
    public function getDeviceID(): ?int;

    public function getSensorName(): ?string;
}
