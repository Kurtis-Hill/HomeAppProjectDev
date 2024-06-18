<?php

namespace App\Services\Sensor\SensorReadingUpdate\RequestReading;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use HttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

interface SensorUpdateCurrentReadingRequestHandlerInterface
{
    /**
     * @throws \App\Exceptions\Sensor\SensorNotFoundException
     * @throws \App\Exceptions\Device\DeviceIPNotSetException
     * @throws \App\Exceptions\Sensor\SensorTypeException
     * @throws ExceptionInterface
     * @throws HttpException
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateTransportMessageDTO $currentReadingUpdateMessageDTO): bool;
}
