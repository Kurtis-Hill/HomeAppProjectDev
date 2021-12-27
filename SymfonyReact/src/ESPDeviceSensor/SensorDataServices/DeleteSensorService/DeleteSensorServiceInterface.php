<?php

namespace App\ESPDeviceSensor\SensorDataServices\DeleteSensorService;

use App\ESPDeviceSensor\Entity\Sensor;
use Doctrine\ORM\ORMException;

interface DeleteSensorServiceInterface
{
    /**
     * @throws ORMException
     */
    public function deleteSensor(Sensor $sensor): void;
}
