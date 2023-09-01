<?php

namespace App\Sensors\SensorServices\DeleteSensorService;

use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;

class DeleteSensorHandler implements DeleteSensorHandlerInterface
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
