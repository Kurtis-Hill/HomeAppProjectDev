<?php

namespace App\Repository\Sensor\ConstRecord;

use App\Entity\Sensor\ConstantRecording\ConstAnalog;
use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstHumid;
use App\Entity\Sensor\ConstantRecording\ConstLatitude;
use App\Entity\Sensor\ConstantRecording\ConstTemp;
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
