<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

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
