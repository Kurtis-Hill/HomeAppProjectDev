<?php

namespace App\Sensors\Factories\OufOfBounds;

use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;

interface OutOfBoundsFactoryInterface
{
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface;
}
