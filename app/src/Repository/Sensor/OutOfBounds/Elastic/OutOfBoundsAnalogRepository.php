<?php

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsAnalogRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->index->refresh();
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
