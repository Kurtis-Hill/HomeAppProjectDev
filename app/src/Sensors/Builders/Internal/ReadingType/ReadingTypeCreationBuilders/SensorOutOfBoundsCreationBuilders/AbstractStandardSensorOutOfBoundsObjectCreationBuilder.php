<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;

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
