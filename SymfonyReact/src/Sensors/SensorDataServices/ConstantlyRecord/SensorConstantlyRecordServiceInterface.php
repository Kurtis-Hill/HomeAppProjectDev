<?php

namespace App\Sensors\SensorDataServices\ConstantlyRecord;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use Doctrine\ORM\Exception\ORMException;

interface SensorConstantlyRecordServiceInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ReadingTypeNotSupportedException
     * @throws ORMException
     */
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void;
}
