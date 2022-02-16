<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;

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
