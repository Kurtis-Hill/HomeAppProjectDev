<?php

namespace App\Services\Sensor\SensorReadingUpdate\CurrentReading;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

interface UpdateCurrentSensorReadingInterface
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingTransportMessageDTO $updateSensorCurrentReadingConsumerDTO,
    ): array;
}
