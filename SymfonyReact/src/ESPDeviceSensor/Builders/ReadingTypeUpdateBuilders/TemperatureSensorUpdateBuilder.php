<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;

class TemperatureSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements SensorUpdateBuilderInterface
{
    public function setNewBoundaryForReadingType(
        SensorTypeInterface $sensorTypeObject,
        UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO
    ): void
    {
        if (!$sensorTypeObject instanceof TemperatureSensorTypeInterface) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED);
        }

        $this->updateStandardSensor($sensorTypeObject->getTempObject(), $updateSensorBoundaryReadingsDTO);
    }

    public function buildUpdateSensorBoundaryReadingsDTO(array $sensorData, SensorTypeInterface $sensorTypeObject): UpdateSensorBoundaryReadingsDTO
    {
        if (!is_callable([$sensorTypeObject, 'getTempObject'], true)) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::OBJECT_NOT_FOUND_MESSAGE,
                    $sensorTypeObject->getSensorTypeName()
                )
            );
        }

        return $this->buildStandardSensorUpdateReadingDTO($sensorTypeObject->getTempObject(), $sensorData);
    }
}
