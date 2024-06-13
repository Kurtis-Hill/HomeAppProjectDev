<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use Doctrine\ORM\Exception\ORMException;

interface SensorOutOfBoundsHandlerInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ORMException
     */
    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void;
}
