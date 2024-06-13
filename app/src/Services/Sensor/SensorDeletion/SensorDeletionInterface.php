<?php

namespace App\Services\Sensor\SensorDeletion;

use App\Entity\Sensor\Sensor;

interface SensorDeletionInterface
{
    public function deleteSensor(Sensor $sensor): bool;
}
