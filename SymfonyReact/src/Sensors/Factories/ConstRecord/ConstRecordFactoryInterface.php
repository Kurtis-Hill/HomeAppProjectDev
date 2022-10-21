<?php

namespace App\Sensors\Factories\ConstRecord;

use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;

interface ConstRecordFactoryInterface
{
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface;
}
