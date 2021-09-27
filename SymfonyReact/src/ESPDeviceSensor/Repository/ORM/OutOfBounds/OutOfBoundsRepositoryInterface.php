<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;

use App\Entity\Sensors\OutOfRangeRecordings\OutOfBoundsEntityInterface;

interface OutOfBoundsRepositoryInterface
{
    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void;

    public function flush(): void;
}
