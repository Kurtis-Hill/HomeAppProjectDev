<?php

namespace App\Sensors\Repository\ORM\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\ArrayShape;

interface ReadingTypeRepositoryInterface
{
    #[ArrayShape([ReadingTypes::class])]
    public function findAll();
}
