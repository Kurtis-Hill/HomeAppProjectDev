<?php

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeTemp;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsTempRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->index->refresh();
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
