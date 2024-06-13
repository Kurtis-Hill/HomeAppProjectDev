<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstTemp;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;

class TemperatureConstRecordObjectBuilder extends AbstractStandardConstRecordBuilder implements ConstRecordObjectBuilderInterface
{
    public function buildConstRecordObject(AllSensorReadingTypeInterface $sensorReadingTypeObject): ConstantlyRecordEntityInterface
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

        $constRecordObject = new ConstTemp();

        $this->buildStandardConstRecordObject(
            $constRecordObject,
            $sensorReadingTypeObject
        );

        return $constRecordObject;
    }
}
