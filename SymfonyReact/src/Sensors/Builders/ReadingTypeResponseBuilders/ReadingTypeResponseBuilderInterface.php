<?php

namespace App\Sensors\Builders\ReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTypeResponseBuilderInterface
{
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): BoundaryReadingTypeResponseInterface;
}
