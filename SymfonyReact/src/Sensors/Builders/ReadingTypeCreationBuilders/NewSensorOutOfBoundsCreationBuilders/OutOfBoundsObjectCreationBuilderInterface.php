<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

interface OutOfBoundsObjectCreationBuilderInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     */
    public function buildOutOfBoundsObject(StandardReadingSensorInterface $sensorReadingTypeObject): OutOfBoundsEntityInterface;
}
