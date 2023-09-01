<?php

namespace App\Devices\Builders\DeviceRequestsArgumentBuilders;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorPinNumberNotSetException;

interface DeviceRequestArgumentBuilderInterface
{
    /**
     * @throws SensorPinNumberNotSetException
     */
    public function buildSensorRequestArguments(Sensor $sensor, BoolCurrentReadingUpdateDTO $requestDTO): DeviceRequestDTOInterface;
}
