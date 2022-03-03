<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTypeRepositoryInterface
{
    public function persist(AllSensorReadingTypeInterface $sensorReadingType): void;

    public function flush(): void;

}
