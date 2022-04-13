<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

class AbstractStandardSensorOutOfBoundsObjectCreationBuilder
{
    protected function buildStandardOutOfBoundObject(
        StandardReadingSensorInterface $sensorReadingObject,
        OutOfBoundsEntityInterface $outOfBoundsObject
    ): void {
        $outOfBoundsObject->setSensorReading($sensorReadingObject->getCurrentReading());
        $outOfBoundsObject->setSensorReadingTypeID($sensorReadingObject);
        $outOfBoundsObject->setCreatedAt();
    }
}
