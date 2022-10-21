<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsAnalogRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        // TODO: Implement persist() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function find(): ?OutOfRangeAnalog
    {
        return null;
    }

    public function findOneBy(): ?OutOfRangeAnalog
    {
        return null;
    }

    #[ArrayShape([OutOfRangeAnalog::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([OutOfRangeAnalog::class])]
    public function findBy(): array
    {
        return [];
    }
}
