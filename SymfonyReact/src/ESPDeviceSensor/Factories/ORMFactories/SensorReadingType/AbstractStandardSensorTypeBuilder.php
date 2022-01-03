<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorBoundaryReadingsDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

abstract class AbstractStandardSensorTypeBuilder
{
    protected function updateStandardSensor(StandardReadingSensorInterface $standardReadingSensor, UpdateSensorBoundaryReadingsDTO $updateSensorBoundaryReadingsDTO): void
    {
        $standardReadingSensor->setHighReading($updateSensorBoundaryReadingsDTO->getHighReading());
        $standardReadingSensor->setLowReading($updateSensorBoundaryReadingsDTO->getLowReading());
        $standardReadingSensor->setConstRecord($updateSensorBoundaryReadingsDTO->getConstRecord());
    }
}
