<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

class AbstractStandardSensorOutOfBoundsObjectCreationBuilder
{
    protected function buildStandardOutOfBoundObject(
        StandardReadingSensorInterface $sensorReadingObject,
        OutOfBoundsEntityInterface $outOfBoundsObject
    ): void
    {
        $outOfBoundsObject->setSensorReading($sensorReadingObject->getCurrentReading());
        $outOfBoundsObject->setSensorReadingTypeID($sensorReadingObject);
        $outOfBoundsObject->setCreatedAt();
    }
}
