<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;

abstract class AbstractStandardSensorOutOfBoundsObjectCreationBuilder
{
    protected function buildStandardOutOfBoundObject(
        StandardReadingSensorInterface $sensorReadingObject,
        OutOfBoundsEntityInterface $outOfBoundsObject
    ): void {
        $outOfBoundsObject->setSensorReading($sensorReadingObject->getCurrentReading());
        $outOfBoundsObject->setBaseSensorReadingType($sensorReadingObject->getBaseReadingType());
        $outOfBoundsObject->setCreatedAt();
    }
}
