<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;

interface ConstantlyRecordRepositoryInterface
{
    public function persist(ConstantlyRecordInterface $sensorReadingData): void;

    public function flush(): void;
}
