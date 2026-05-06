<?php

namespace App\Builders\Device\DeviceRequestsArgumentBuilders;

use App\DTOs\Device\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\SensorPinNumberNotSetException;

interface DeviceRequestArgumentBuilderInterface
{
    /**
     * @throws SensorPinNumberNotSetException
     */
    public function buildSensorRequestArguments(Sensor $sensor, BoolCurrentReadingUpdateDTO $requestDTO): DeviceRequestDTOInterface;
}
