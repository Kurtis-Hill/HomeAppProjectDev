<?php

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsLatitudeRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->index->refresh();
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
