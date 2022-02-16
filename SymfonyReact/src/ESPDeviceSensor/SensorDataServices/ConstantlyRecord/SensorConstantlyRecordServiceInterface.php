<?php

namespace App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use Doctrine\ORM\ORMException;

interface SensorConstantlyRecordServiceInterface
{
    /**
     * @throws ReadingTypeNotExpectedException
     * @throws ReadingTypeNotSupportedException
     * @throws ORMException
     */
    public function checkAndProcessConstRecord(AllSensorReadingTypeInterface $readingTypeObject): void;
}
