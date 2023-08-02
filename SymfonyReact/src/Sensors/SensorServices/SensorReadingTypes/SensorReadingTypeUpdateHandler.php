<?php

namespace App\Sensors\SensorServices\SensorReadingTypes;

use App\Sensors\Entity\SensorTypes\Interfaces\ReadingIntervalInterface;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;

class SensorReadingTypeUpdateHandler
{
    private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory;

    public function __construct(SensorTypeRepositoryFactory $sensorTypeRepositoryFactory)
    {
        $this->sensorTypeRepositoryFactory = $sensorTypeRepositoryFactory;
    }

    public function updateSensorReadingTypeInterval(int $sensorID, string $sensorReadingType, int $interval): bool
    {
        $sensorReadingTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensorReadingType);

        $sensorReadingTypeObject = $sensorReadingTypeRepository->findOneBy(['sensor' => $sensorID]);

        if (!$sensorReadingTypeObject instanceof ReadingIntervalInterface) {
            return false;
        }

        $sensorReadingTypeObject->setReadingInterval($interval);

        $sensorReadingTypeRepository->flush();

        return true;
    }
}
