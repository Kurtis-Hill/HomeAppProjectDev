<?php

namespace App\Sensors\Builders\ReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType\BoundaryReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTypeResponseBuilderInterface
{
    public function buildReadingTypeBoundaryReadingsResponseDTO(
        AllSensorReadingTypeInterface $readingTypeObject
    ): BoundaryReadingTypeResponseInterface;
}
