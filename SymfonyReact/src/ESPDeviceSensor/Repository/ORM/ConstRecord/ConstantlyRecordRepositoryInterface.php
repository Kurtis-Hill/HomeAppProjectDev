<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use Doctrine\ORM\ORMException;

interface ConstantlyRecordRepositoryInterface
{
    /**
     * @throws ORMException
     */
    public function persist(ConstantlyRecordInterface $sensorReadingData): void;

    public function flush(): void;
}
