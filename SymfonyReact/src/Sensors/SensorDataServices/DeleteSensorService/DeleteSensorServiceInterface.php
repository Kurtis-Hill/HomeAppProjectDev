<?php

namespace App\Sensors\SensorDataServices\DeleteSensorService;

use App\Sensors\Entity\Sensor;
use Doctrine\ORM\Exception\ORMException;

interface DeleteSensorServiceInterface
{
    /**
     * @throws ORMException
     */
    public function deleteSensor(Sensor $sensor): void;
}
