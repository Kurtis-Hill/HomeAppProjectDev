<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

abstract class AbstractStandardConstRecordBuilder
{
    protected function buildStandardConstRecordObject(
        ConstantlyRecordInterface $constantlyRecordObject,
        AllSensorReadingTypeInterface $standardReadingSensor
    ): void
    {
        $constantlyRecordObject->setSensorReading($standardReadingSensor->getCurrentReading());
        $constantlyRecordObject->setSensorReadingTypeObject($standardReadingSensor);
        $constantlyRecordObject->setCreatedAt();
    }
}
