<?php

namespace App\Sensors\SensorServices\SensorDeletion;

use App\Sensors\Entity\Sensor;

interface SensorDeletionInterface
{
    public function deleteSensor(Sensor $sensor): bool;
}
