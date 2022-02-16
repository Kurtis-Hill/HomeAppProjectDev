<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ConstantRecording\ConstTemp;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;

class TemperatureConstRecordObjectBuilder extends AbstractStandardConstRecordBuilder implements ConstRecordObjectBuilderInterface
{
    public function buildConstRecordObject(AllSensorReadingTypeInterface $sensorReadingTypeObject): ConstantlyRecordInterface
    {
        if (!$sensorReadingTypeObject instanceof Temperature) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    Temperature::READING_TYPE
                )
            );
        }

        $constRecordObject = new ConstTemp();

        $this->buildStandardConstRecordObject(
            $constRecordObject,
            $sensorReadingTypeObject
        );

        return $constRecordObject;
    }
}
