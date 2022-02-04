<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;

class LatitudeSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements SensorUpdateBuilderInterface
{
    public function setNewBoundaryForReadingType(SensorTypeInterface $sensorTypeObject, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        if (!$sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED);
        }

        $this->updateStandardSensor($sensorTypeObject->getLatitudeObject(), $updateSensorBoundaryReadingsDTO);
    }

    public function buildUpdateSensorBoundaryReadingsDTO(array $sensorData, SensorTypeInterface $sensorTypeObject): UpdateSensorBoundaryReadingsDTO
    {
        if (!is_callable([$sensorTypeObject, 'getLatitudeObject'], true)) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::OBJECT_NOT_FOUND_MESSAGE,
                    $sensorTypeObject->getSensorTypeName()
                )
            );
        }

        return $this->buildStandardSensorUpdateReadingDTO($sensorTypeObject->getLatitudeObject(), $sensorData);
    }
}
