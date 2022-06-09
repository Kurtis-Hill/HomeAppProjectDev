<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

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
