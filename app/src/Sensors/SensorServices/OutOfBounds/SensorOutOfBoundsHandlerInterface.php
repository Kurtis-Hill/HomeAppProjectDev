<?php

namespace App\Sensors\SensorServices\OutOfBounds;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use Doctrine\ORM\Exception\ORMException;

interface SensorOutOfBoundsHandlerInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ORMException
     */
    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void;
}
