<?php

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeHumid;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;


class OutOfBoundsHumidityRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->index->refresh();
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
