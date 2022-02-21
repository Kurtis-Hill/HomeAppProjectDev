<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\ArrayShape;

interface ReadingTypeRepositoryInterface
{
    #[ArrayShape([ReadingTypes::class])]
    public function findAll();
}
