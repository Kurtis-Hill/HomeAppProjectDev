<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface SensorUpdateCurrentReadingRequestHandlerInterface
{
    /**
     * @throws SensorNotFoundException
     * @throws DeviceIPNotSetException
     * @throws SensorTypeException
     * @throws ExceptionInterface
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO): bool;
}
