<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordTemperatureRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function find(): ?ConstTemp
    {
        return null;
    }

    public function findOneBy(): ?ConstTemp
    {
        return null;
    }

    #[ArrayShape([ConstTemp::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([ConstTemp::class])]
    public function findBy(): array
    {
        return [];
    }
}
