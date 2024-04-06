<?php

namespace App\Sensors\Builders\Request\RequestSensorCurrentReadingUpdateArgumentBuilder;

use App\Devices\Builders\DeviceRequestsArgumentBuilders\DeviceRequestArgumentBuilderInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\SendRequests\RequestSensorCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorPinNumberNotSetException;

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
