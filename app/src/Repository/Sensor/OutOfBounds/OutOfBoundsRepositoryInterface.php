<?php

namespace App\Repository\Sensor\OutOfBounds;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeHumid;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeTemp;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method OutOfRangeHumid|OutOfRangeAnalog|OutOfRangeTemp|OutOfRangeLatitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfRangeHumid|OutOfRangeAnalog|OutOfRangeTemp|OutOfRangeLatitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfRangeHumid[]|OutOfRangeAnalog[]|OutOfRangeTemp[]|OutOfRangeLatitude[]    findAll()
 * @method OutOfRangeHumid[]|OutOfRangeAnalog[]|OutOfRangeTemp[]|OutOfRangeLatitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
