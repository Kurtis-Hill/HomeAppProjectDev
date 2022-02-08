<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use JetBrains\PhpStorm\Pure;

class AnalogSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements SensorUpdateBuilderInterface
{
    public function setNewBoundaryForReadingType(SensorTypeInterface $sensorTypeObject, UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        if (!$sensorTypeObject instanceof AnalogSensorTypeInterface) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED);
        }

        $this->updateStandardSensor($sensorTypeObject->getAnalogObject(), $updateSensorBoundaryReadingsDTO);
    }

    public function buildUpdateSensorBoundaryReadingsDTO(
        array $sensorData,
        AllSensorReadingTypeInterface $sensorReadingTypeObject
    ): UpdateStandardSensorBoundaryReadingsDTO
    {
        if (!$sensorReadingTypeObject instanceof Analog) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    $sensorReadingTypeObject->getReadingType(),
                    $sensorData['readingType'],
                )
            );
        }

        return $this->buildStandardSensorUpdateReadingDTO($sensorData, $sensorReadingTypeObject);
    }
}
