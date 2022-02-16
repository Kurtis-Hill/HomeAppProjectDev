<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord;

use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;

interface ORMConstRecordFactoryInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface;
}
