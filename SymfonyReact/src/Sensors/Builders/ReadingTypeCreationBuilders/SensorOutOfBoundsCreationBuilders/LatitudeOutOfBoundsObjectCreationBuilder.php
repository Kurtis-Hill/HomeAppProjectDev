<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

class LatitudeOutOfBoundsObjectCreationBuilder extends AbstractStandardSensorOutOfBoundsObjectCreationBuilder implements OutOfBoundsObjectCreationBuilderInterface
{
    public function buildOutOfBoundsObject(StandardReadingSensorInterface $sensorReadingTypeObject): OutOfBoundsEntityInterface
    {
        if (!$sensorReadingTypeObject instanceof Latitude) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    Latitude::getReadingTypeName()
                )
            );
        }

        $outOfBoundsObject = new OutOfRangeLatitude();

        $this->buildStandardOutOfBoundObject(
            $sensorReadingTypeObject,
            $outOfBoundsObject,
        );

        return $outOfBoundsObject;
    }
}
