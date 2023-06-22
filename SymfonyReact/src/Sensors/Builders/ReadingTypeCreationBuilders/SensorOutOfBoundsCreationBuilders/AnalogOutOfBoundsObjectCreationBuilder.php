<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

class AnalogOutOfBoundsObjectCreationBuilder extends AbstractStandardSensorOutOfBoundsObjectCreationBuilder implements OutOfBoundsObjectCreationBuilderInterface
{
    public function buildOutOfBoundsObject(StandardReadingSensorInterface $sensorReadingTypeObject): OutOfBoundsEntityInterface
    {
        if (!$sensorReadingTypeObject instanceof Analog) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    Analog::READING_TYPE
                )
            );
        }

        $outOfBoundsObject = new OutOfRangeAnalog();

        $this->buildStandardOutOfBoundObject(
            $sensorReadingTypeObject,
            $outOfBoundsObject,
        );

        return $outOfBoundsObject;
    }
}
