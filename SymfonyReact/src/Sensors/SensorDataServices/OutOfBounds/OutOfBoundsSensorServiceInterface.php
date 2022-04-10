<?php

namespace App\Sensors\SensorDataServices\OutOfBounds;

use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use Doctrine\ORM\ORMException;

interface OutOfBoundsSensorServiceInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ORMException
     */
    public function checkAndProcessOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void;
}
