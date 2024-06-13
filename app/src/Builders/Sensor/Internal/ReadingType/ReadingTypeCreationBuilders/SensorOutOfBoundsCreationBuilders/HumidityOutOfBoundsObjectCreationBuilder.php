<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeHumid;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;

class HumidityOutOfBoundsObjectCreationBuilder extends AbstractStandardSensorOutOfBoundsObjectCreationBuilder implements OutOfBoundsObjectCreationBuilderInterface
{
    public function buildOutOfBoundsObject(StandardReadingSensorInterface $sensorReadingTypeObject): OutOfBoundsEntityInterface
    {
        if (!$sensorReadingTypeObject instanceof Humidity) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    Humidity::READING_TYPE
                )
            );
        }

        $outOfBoundsObject = new OutOfRangeHumid();

        $this->buildStandardOutOfBoundObject(
            $sensorReadingTypeObject,
            $outOfBoundsObject,
        );

        return $outOfBoundsObject;
    }
}
