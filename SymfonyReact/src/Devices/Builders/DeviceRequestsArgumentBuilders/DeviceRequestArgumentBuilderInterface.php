<?php

namespace App\Devices\Builders\DeviceRequestsArgumentBuilders;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\Sensor;

interface DeviceRequestArgumentBuilderInterface
{
    public function buildSensorRequestArguments(Sensor $sensor, AbstractCurrentReadingUpdateRequestDTO $requestDTO): DeviceRequestDTOInterface;
}
