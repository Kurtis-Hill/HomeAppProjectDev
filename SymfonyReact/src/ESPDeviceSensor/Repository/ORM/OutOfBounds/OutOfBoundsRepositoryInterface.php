<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;


use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use Doctrine\ORM\ORMException;

interface OutOfBoundsRepositoryInterface
{
    /**
     * @throws ORMException
     */
    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;
}
