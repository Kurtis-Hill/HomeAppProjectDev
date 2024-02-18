<?php

namespace App\Sensors\SensorServices\SensorDeletion;

use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class SensorDeletionHandler implements SensorDeletionInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private LoggerInterface $logger;

    public function __construct(SensorRepositoryInterface $sensorRepository, LoggerInterface $logger)
    {
        $this->sensorRepository = $sensorRepository;
        $this->logger = $logger;
    }

    public function deleteSensor(Sensor $sensor): bool
    {
        try {
            $this->sensorRepository->remove($sensor);
            $this->sensorRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove sensor', [$e->getMessage()]);

            return false;
        }

        return true;
    }
}
