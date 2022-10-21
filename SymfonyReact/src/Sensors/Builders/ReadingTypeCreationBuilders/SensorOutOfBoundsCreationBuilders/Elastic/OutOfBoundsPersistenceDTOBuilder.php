<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\Elastic;

use App\Sensors\DTO\Request\OutOfBounds\Elastic\OutOfBoundsElasticPersistenceDTO;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;

class OutOfBoundsPersistenceDTOBuilder
{
    public static function buildOutOfBoundsPersistenceDTO(OutOfBoundsEntityInterface $outOfBoundsEntity): OutOfBoundsElasticPersistenceDTO
    {
        return new OutOfBoundsElasticPersistenceDTO(
            $outOfBoundsEntity->getSensorReadingID()->getSensorID(),
            $outOfBoundsEntity->getSensorReading(),
            $outOfBoundsEntity->getCreatedAt(),
        );
    }
}
