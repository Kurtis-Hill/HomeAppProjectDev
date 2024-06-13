<?php

namespace App\Services\Sensor\ConstantlyRecord;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
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
