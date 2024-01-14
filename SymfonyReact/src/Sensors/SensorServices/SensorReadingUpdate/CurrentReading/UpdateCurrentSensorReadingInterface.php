<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;

interface UpdateCurrentSensorReadingInterface
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SensorReadingTypeObjectNotFoundException
     */
    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO,
    ): array;
}
