<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

abstract class AbstractStandardConstRecordBuilder
{
    protected function buildStandardConstRecordObject(
        ConstantlyRecordInterface $constantlyRecordObject,
        AllSensorReadingTypeInterface $standardReadingSensor
    ): void {
        $constantlyRecordObject->setSensorReading($standardReadingSensor->getCurrentReading());
        $constantlyRecordObject->setSensorReadingTypeObject($standardReadingSensor);
        $constantlyRecordObject->setCreatedAt();
    }
}
