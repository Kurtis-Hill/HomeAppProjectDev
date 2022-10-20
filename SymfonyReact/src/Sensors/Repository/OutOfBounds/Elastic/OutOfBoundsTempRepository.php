<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsTempRepository implements OutOfBoundsRepositoryInterface
{
    public const ES_INDEX = 'outofbounds_temp';

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        // TODO: Implement persist() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function find(): ?OutOfRangeTemp
    {
        return null;
    }

    public function findOneBy(): ?OutOfRangeTemp
    {
        return null;
    }

    #[ArrayShape([OutOfRangeTemp::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([OutOfRangeTemp::class])]
    public function findBy(): array
    {
        return [];
    }
}
