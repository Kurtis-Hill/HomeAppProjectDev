<?php

namespace App\Services\Sensor\UpdateSensor;

use App\DTOs\Sensor\Internal\Sensor\UpdateSensorDTO;
use App\Exceptions\Sensor\DuplicateSensorException;

interface UpdateSensorInterface
{
    /**
     * @throws DuplicateSensorException
     */
    public function handleSensorUpdate(UpdateSensorDTO $updateSensorDTO): array;
}
