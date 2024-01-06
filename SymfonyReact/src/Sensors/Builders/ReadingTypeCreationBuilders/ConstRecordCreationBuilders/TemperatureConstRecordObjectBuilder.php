<?php
declare(strict_types=1);

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;

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
