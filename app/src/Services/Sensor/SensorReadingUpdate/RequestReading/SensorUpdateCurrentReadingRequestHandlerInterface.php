<?php

namespace App\Services\Sensor\SensorReadingUpdate\RequestReading;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\Exceptions\Device\DeviceIPNotSetException;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorTypeException;
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
