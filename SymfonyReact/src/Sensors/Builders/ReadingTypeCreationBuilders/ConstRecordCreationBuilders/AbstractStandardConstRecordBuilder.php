<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

abstract class AbstractStandardConstRecordBuilder
{
    protected function buildStandardConstRecordObject(
        ConstantlyRecordEntityInterface $constantlyRecordObject,
        AllSensorReadingTypeInterface $standardReadingSensor
    ): void {
        $constantlyRecordObject->setSensorReading($standardReadingSensor->getCurrentReading());
        $constantlyRecordObject->setSensorReadingObject($standardReadingSensor);
        $constantlyRecordObject->setCreatedAt();
    }
}
