<?php

namespace App\ESPDeviceSensor\SensorDataServices\DeleteSensorService;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;

class DeleteSensorService implements DeleteSensorServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    public function __construct(SensorRepositoryInterface $sensorRepository)
    {
        $this->sensorRepository = $sensorRepository;
    }

    public function deleteSensor(Sensor $sensor): void
    {
        $this->sensorRepository->remove($sensor);
        $this->sensorRepository->flush();
    }
}
