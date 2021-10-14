<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds;

use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;

interface OutOfBoundsFactoryInterface
{
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface;
}
