<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateReadingTypeCurrentReadingDTO;
use App\ESPDeviceSensor\DTO\Sensor\UpdateStandardSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeObjectBuilderException;

class LatitudeSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements SensorUpdateBuilderInterface
{
    public function setNewBoundaryForReadingType(SensorTypeInterface $sensorTypeObject, UpdateStandardSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        if (!$sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED);
        }

        $this->updateStandardSensor($sensorTypeObject->getLatitudeObject(), $updateSensorBoundaryReadingsDTO);
    }

    public function buildUpdateSensorBoundaryReadingsDTO(
        array $sensorData,
        AllSensorReadingTypeInterface $sensorReadingTypeObject,
    ): UpdateStandardSensorBoundaryReadingsDTO
    {
        if (!$sensorReadingTypeObject instanceof Latitude) {
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

    public function buildCurrentReadingUpdateDTO(
        AllSensorReadingTypeInterface $allSensorReadingType,
        array $sensorData
    ): UpdateReadingTypeCurrentReadingDTO
    {
        if (!$allSensorReadingType instanceof Latitude) {
            throw new ReadingTypeNotExpectedException(
                sprintf(
                    ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED,
                    Latitude::READING_TYPE,
                    $allSensorReadingType->getReadingType(),
                )
            );
        }
        if (empty($sensorData['latitudeReading'])) {
            throw new ReadingTypeObjectBuilderException(
                sprintf(
                    ReadingTypeObjectBuilderException::CURRENT_READING_FAILED_TO_BUILD_FOR_TYPE,
                    Latitude::READING_TYPE,
                )
            );
        }

        return $this->updateStandardSensorCurrentReading(
            $allSensorReadingType,
            $sensorData['latitudeReading']
        );
    }
}
