<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use Doctrine\ORM\ORMException;

interface OutOfBoundsSensorServiceInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ORMException
     */
    public function checkAndHandleSensorReadingOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void;
}
