<?php

namespace App\Sensors\Repository\OutOfBounds\Elastic;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class OutOfBoundsTempRepository extends AbstractOutOfBoundsRepository implements OutOfBoundsRepositoryInterface
{
    public function flush(): void
    {
        $this->_em->flush();
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
