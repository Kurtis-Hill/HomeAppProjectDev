<?php

namespace App\Factories\Sensor\OufOfBounds;

use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;

interface OutOfBoundsFactoryInterface
{
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface;
}
