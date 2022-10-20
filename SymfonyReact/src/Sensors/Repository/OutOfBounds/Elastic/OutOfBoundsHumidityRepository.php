<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;


class OutOfBoundsHumidityRepository implements OutOfBoundsRepositoryInterface
{
    public const ES_INDEX = 'outofbounds_humidity';

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        // TODO: Implement persist() method.
    }

    public function find(): ?OutOfRangeHumid
    {
        return null;
    }

    public function findOneBy(): ?OutOfRangeHumid
    {
        return null;
    }

    #[ArrayShape([OutOfRangeHumid::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([OutOfRangeHumid::class])]
    public function findBy(): array
    {
        return [];
    }
}
