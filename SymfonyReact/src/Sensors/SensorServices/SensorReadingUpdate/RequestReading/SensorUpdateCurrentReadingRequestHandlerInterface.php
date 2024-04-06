<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use HttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface SensorUpdateCurrentReadingRequestHandlerInterface
{
    /**
     * @throws SensorNotFoundException
     * @throws DeviceIPNotSetException
     * @throws SensorTypeException
     * @throws ExceptionInterface
     * @throws HttpException
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateTransportMessageDTO $currentReadingUpdateMessageDTO): bool;
}
