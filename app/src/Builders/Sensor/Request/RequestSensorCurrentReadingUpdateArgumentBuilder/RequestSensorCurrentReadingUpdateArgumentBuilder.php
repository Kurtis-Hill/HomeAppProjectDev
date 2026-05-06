<?php

namespace App\Builders\Sensor\Request\RequestSensorCurrentReadingUpdateArgumentBuilder;

use App\Builders\Device\DeviceRequestsArgumentBuilders\DeviceRequestArgumentBuilderInterface;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\DTOs\Sensor\Request\SendRequests\RequestSensorCurrentReadingUpdateRequestDTO;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\SensorPinNumberNotSetException;

class RequestSensorCurrentReadingUpdateArgumentBuilder implements DeviceRequestArgumentBuilderInterface
{
    /**
     * @throws SensorPinNumberNotSetException
     */
    public function buildSensorRequestArguments(Sensor $sensor, BoolCurrentReadingUpdateDTO $requestDTO): RequestSensorCurrentReadingUpdateRequestDTO
    {
        if ($sensor->getPinNumber() === null) {
            throw new SensorPinNumberNotSetException(sprintf(SensorPinNumberNotSetException::MESSAGE, $sensor->getSensorID()));
        }

        return new RequestSensorCurrentReadingUpdateRequestDTO(
            $sensor->getSensorName(),
            $sensor->getPinNumber(),
            $requestDTO->getCurrentReading()
        );
    }
}
