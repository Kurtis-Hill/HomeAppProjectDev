<?php

namespace App\Sensors\SensorServices\DeleteSensorService;

use App\Sensors\Entity\Sensor;
use Doctrine\ORM\Exception\ORMException;

interface DeleteSensorHandlerInterface
{
    /**
     * @throws ORMException
     */
    public function deleteSensor(Sensor $sensor): void;
}
