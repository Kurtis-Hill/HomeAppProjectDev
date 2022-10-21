<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsLatitudeRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public const ES_INDEX = 'outofbounds_latitude';

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        // TODO: Implement persist() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function find(): ?OutOfRangeLatitude
    {
        return null;
    }

    public function findOneBy(): ?OutOfRangeLatitude
    {
        return null;
    }

    #[ArrayShape([OutOfRangeLatitude::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([OutOfRangeLatitude::class])]
    public function findBy(): array
    {
        return [];
    }
}
