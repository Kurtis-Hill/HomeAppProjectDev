<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsAnalogRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->_em->flush();
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
