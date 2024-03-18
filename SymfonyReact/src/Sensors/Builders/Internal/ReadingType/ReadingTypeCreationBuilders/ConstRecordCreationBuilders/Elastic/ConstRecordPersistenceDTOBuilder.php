<?php
declare(strict_types=1);

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\Elastic;

use App\Sensors\DTO\Request\ConstRecord\Elastic\ConstRecordElasticPersistenceDTO;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;

class ConstRecordPersistenceDTOBuilder
{
    public static function buildConstRecordElasticPersistenceDTO(ConstantlyRecordEntityInterface $outOfBoundsEntity): ConstRecordElasticPersistenceDTO
    {
        return new ConstRecordElasticPersistenceDTO(
            $outOfBoundsEntity->getSensorReadingObject()->getSensorID(),
            $outOfBoundsEntity->getSensorReading(),
            $outOfBoundsEntity->getCreatedAt(),
        );
    }
}