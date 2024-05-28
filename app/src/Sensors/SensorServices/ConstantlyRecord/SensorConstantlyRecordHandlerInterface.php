<?php

namespace App\Sensors\SensorServices\ConstantlyRecord;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use Doctrine\ORM\Exception\ORMException;

interface SensorConstantlyRecordHandlerInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ReadingTypeNotSupportedException
     * @throws ORMException
     */
    public function processConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void;
}
