<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeUpdateBuilders;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function buildStandardSensorUpdateReadingDTO(StandardReadingSensorInterface $standardReadingSensor, array $sensorData): UpdateSensorBoundaryReadingsDTO
    {
        return new UpdateSensorBoundaryReadingsDTO(
            $standardReadingSensor->getSensorID(),
            $sensorData['sensorType'],
            $sensorData['highReading'],
            $sensorData['lowReading'],
            $sensorData['constRecord'],
            $standardReadingSensor->getHighReading(),
            $standardReadingSensor->getLowReading(),
        );
    }
    protected function updateStandardSensor(StandardReadingSensorInterface $standardReadingSensor, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }
}
