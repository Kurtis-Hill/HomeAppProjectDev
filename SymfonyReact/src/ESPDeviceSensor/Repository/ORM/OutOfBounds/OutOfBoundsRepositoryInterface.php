<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;


use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface OutOfBoundsRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;
}
