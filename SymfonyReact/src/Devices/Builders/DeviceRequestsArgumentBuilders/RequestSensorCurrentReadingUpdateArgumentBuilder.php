<?php

namespace App\Devices\Builders\DeviceRequestsArgumentBuilders;

use App\Devices\DTO\Request\DeviceRequest\RequestSensorCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\Sensor;

class RequestSensorCurrentReadingUpdateArgumentBuilder implements DeviceRequestArgumentBuilderInterface
{
    public function buildSensorRequestArguments(Sensor $sensor, AbstractCurrentReadingUpdateRequestDTO $requestDTO): RequestSensorCurrentReadingUpdateRequestDTO
    {
        return new RequestSensorCurrentReadingUpdateRequestDTO(
            $sensor->getSensorName(),
            1,
            $requestDTO->getCurrentReading()
        );
    }
}
