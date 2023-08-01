<?php

namespace App\Devices\Builders\DeviceRequestsArgumentBuilders;

use App\Devices\DTO\Request\DeviceRequest\RequestSensorCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
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
