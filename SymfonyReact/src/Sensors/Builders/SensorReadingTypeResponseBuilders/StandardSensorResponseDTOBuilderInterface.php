<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

interface StandardSensorResponseDTOBuilderInterface
{
    public function buildSensorReadingTypeResponseDTO(StandardReadingSensorInterface $analog): StandardReadingTypeResponseInterface;
}
