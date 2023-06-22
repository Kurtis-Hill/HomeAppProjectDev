<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

class AnalogConstRecordObjectBuilder extends AbstractStandardConstRecordBuilder implements ConstRecordObjectBuilderInterface
{
    public function buildConstRecordObject(AllSensorReadingTypeInterface $sensorReadingTypeObject): ConstantlyRecordEntityInterface
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

        $constRecordObject = new ConstAnalog();

        $this->buildStandardConstRecordObject(
            $constRecordObject,
            $sensorReadingTypeObject
        );

        return $constRecordObject;
    }
}
