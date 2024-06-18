<?php

namespace App\Factories\Sensor\ConstRecord;

use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;

interface ConstRecordFactoryInterface
{
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface;
}
