<?php

namespace App\Sensors\SensorServices\UpdateSensor;

use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\UpdateSensorDetailsRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DeviceNotFoundException;
use App\Sensors\Exceptions\DuplicateSensorException;

interface UpdateSensorInterface
{
    /**
     * @throws DuplicateSensorException
     */
    public function handleSensorUpdate(UpdateSensorDTO $updateSensorDTO): array;
}
