<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;

interface ReadingTypeRepositoryInterface
{
    public function persist(AllSensorReadingTypeInterface $sensorReadingType): void;

    public function flush(): void;

}
