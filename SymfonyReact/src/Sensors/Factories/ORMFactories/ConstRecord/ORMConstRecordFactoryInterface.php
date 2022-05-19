<?php

namespace App\Sensors\Factories\ORMFactories\ConstRecord;

use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryInterface;

interface ORMConstRecordFactoryInterface
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getConstRecordServiceRepository(string $sensorReadingTypeObject): ConstantlyRecordRepositoryInterface;
}
