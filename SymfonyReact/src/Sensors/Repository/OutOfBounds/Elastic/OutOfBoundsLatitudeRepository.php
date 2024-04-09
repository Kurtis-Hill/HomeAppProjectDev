<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Elastica\Exception\NotImplementedException;
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
