<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds;

use App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsRepositoryInterface;

interface OutOfBoundsORMFactoryInterface
{
    public function getOutOfBoundsServiceRepository(string $sensorReadingType): OutOfBoundsRepositoryInterface;
}
