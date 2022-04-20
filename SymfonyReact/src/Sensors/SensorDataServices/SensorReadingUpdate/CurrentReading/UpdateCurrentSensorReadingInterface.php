<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

interface UpdateCurrentSensorReadingInterface
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handleUpdateSensorCurrentReading(UpdateSensorCurrentReadingMessageDTO $updateSensorCurrentReadingConsumerDTO, Devices $device): bool;
}
