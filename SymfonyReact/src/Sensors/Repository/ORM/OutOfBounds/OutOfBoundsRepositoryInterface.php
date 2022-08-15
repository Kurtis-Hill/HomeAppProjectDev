<?php

namespace App\Sensors\Repository\ORM\OutOfBounds;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
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
