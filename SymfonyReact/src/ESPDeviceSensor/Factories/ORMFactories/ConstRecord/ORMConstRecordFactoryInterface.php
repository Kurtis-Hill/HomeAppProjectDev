<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord;

use App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;

interface ORMConstRecordFactoryInterface
{
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface;
}
