<?php

namespace App\Sensors\Repository\ORM\OutOfBounds;


use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
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
