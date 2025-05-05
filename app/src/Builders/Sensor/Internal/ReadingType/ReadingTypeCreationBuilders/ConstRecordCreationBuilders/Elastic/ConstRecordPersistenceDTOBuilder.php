<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\Elastic;

use App\DTOs\Sensor\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO;
use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;

class ConstRecordPersistenceDTOBuilder
{
    public static function buildConstRecordElasticPersistenceDTO(ConstantlyRecordEntityInterface $outOfBoundsEntity): ConstRecordElasticPersistenceDTO
    {
        return new ConstRecordElasticPersistenceDTO(
            $outOfBoundsEntity->getSensorReadingObject()->getSensor()->getSensorID(),
            $outOfBoundsEntity->getSensorReading(),
            $outOfBoundsEntity->getCreatedAt(),
        );
    }
}
