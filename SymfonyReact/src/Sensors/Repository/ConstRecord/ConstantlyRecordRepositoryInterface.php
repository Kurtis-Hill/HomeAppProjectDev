<?php

namespace App\Sensors\Repository\ConstRecord;

use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstHumid;
use App\Sensors\Entity\ConstantRecording\ConstLatitude;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method ConstAnalog|ConstHumid|ConstLatitude|ConstTemp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstAnalog|ConstHumid|ConstLatitude|ConstTemp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstAnalog[]|ConstHumid[]|ConstLatitude[]|ConstTemp[]    findAll()
 * @method ConstAnalog[]|ConstHumid[]|ConstLatitude[]|ConstTemp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ConstantlyRecordRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(ConstantlyRecordEntityInterface $sensorReadingData): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;
}
