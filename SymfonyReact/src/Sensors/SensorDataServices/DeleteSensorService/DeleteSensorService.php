<?php

namespace App\Sensors\SensorDataServices\DeleteSensorService;

use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;

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
