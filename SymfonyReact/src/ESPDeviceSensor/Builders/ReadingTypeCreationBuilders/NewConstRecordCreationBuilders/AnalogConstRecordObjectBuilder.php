<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstAnalog;
use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;

class AnalogConstRecordObjectBuilder extends AbstractStandardConstRecordBuilder implements ConstRecordObjectBuilderInterface
{
    public function buildConstRecordObject(AllSensorReadingTypeInterface $sensorReadingTypeObject): ConstantlyRecordInterface
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
