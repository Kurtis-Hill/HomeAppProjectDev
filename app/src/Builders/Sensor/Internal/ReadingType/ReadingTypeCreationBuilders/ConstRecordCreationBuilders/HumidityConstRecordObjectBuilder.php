<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstHumid;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;

class HumidityConstRecordObjectBuilder extends AbstractStandardConstRecordBuilder implements ConstRecordObjectBuilderInterface
{
    public function buildConstRecordObject(AllSensorReadingTypeInterface $sensorReadingTypeObject): ConstantlyRecordEntityInterface
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

        $constRecordObject = new ConstHumid();

        $this->buildStandardConstRecordObject(
            $constRecordObject,
            $sensorReadingTypeObject
        );

        return $constRecordObject;
    }
}
