<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeTemp;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;

class TemperatureOutOfBoundsObjectCreationBuilder extends AbstractStandardSensorOutOfBoundsObjectCreationBuilder implements OutOfBoundsObjectCreationBuilderInterface
{
    public function buildOutOfBoundsObject(StandardReadingSensorInterface $sensorReadingTypeObject): OutOfBoundsEntityInterface
    {
        if (!$sensorReadingTypeObject instanceof Temperature) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    Temperature::getReadingTypeName()
                )
            );
        }

        $outOfBoundsObject = new OutOfRangeTemp();

        $this->buildStandardOutOfBoundObject(
            $sensorReadingTypeObject,
            $outOfBoundsObject,
        );

        return $outOfBoundsObject;
    }
}
