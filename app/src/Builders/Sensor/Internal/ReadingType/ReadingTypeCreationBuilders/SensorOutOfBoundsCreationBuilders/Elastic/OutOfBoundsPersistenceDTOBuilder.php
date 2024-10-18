<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\Elastic;

use App\DTOs\Sensor\Request\OutOfBounds\Elastic\OutOfBoundsElasticPersistenceDTO;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;

class OutOfBoundsPersistenceDTOBuilder
{
    public static function buildOutOfBoundsPersistenceDTO(OutOfBoundsEntityInterface $outOfBoundsEntity): OutOfBoundsElasticPersistenceDTO
    {
        return new OutOfBoundsElasticPersistenceDTO(
            $outOfBoundsEntity->getBaseSensorReadingType()->getBaseReadingTypeID(),
            $outOfBoundsEntity->getSensorReading(),
            $outOfBoundsEntity->getCreatedAt(),
        );
    }
}
