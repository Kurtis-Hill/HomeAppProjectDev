<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\AbstractStandardSensorTypeBuilder;

class AnalogSensorUpdateBuilder extends AbstractStandardSensorTypeBuilder implements SensorUpdateBuilderInterface
{
    public function setNewBoundaryForReadingType(SensorTypeInterface $sensorTypeObject, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        if (!$sensorTypeObject instanceof AnalogSensorTypeInterface) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotExpectedException::READING_TYPE_NOT_EXPECTED);
        }

        $this->updateStandardSensor($sensorTypeObject->getAnalogObject(), $updateSensorBoundaryReadingsDTO);
    }

    public function buildUpdateSensorBoundaryReadingsDTO(array $sensorData, SensorTypeInterface $sensorTypeObject): UpdateSensorBoundaryReadingsDTO
    {
        return new UpdateSensorBoundaryReadingsDTO(
            // $sensorTypeObject->getAnalogObject()->getSensorID(),
            $sensorData['sensorType'],
            $sensorData['highReading'],
            $sensorData['lowReading'],
            $sensorData['constRecord'],
        );
    }
}
