<?php
declare(strict_types=1);

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

abstract class AbstractStandardConstRecordBuilder
{
    protected function buildStandardConstRecordObject(
        ConstantlyRecordEntityInterface $constantlyRecordObject,
        AllSensorReadingTypeInterface $standardReadingSensor
    ): void {
        $constantlyRecordObject->setSensorReading($standardReadingSensor->getCurrentReading());
        $constantlyRecordObject->setSensorReadingObject($standardReadingSensor->getBaseReadingType());
        $constantlyRecordObject->setCreatedAt();
    }
}