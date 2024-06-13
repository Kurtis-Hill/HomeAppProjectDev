<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\Elastic;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;

class ConstRecordPersistenceDTOBuilder
{
    public static function buildConstRecordElasticPersistenceDTO(ConstantlyRecordEntityInterface $outOfBoundsEntity): \App\DTOs\Sensor\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO
    {
        return new \App\DTOs\Sensor\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO(
            $outOfBoundsEntity->getSensorReadingObject()->getSensorID(),
            $outOfBoundsEntity->getSensorReading(),
            $outOfBoundsEntity->getCreatedAt(),
        );
    }
}
