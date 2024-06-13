<?php

namespace App\Repository\Sensor\ConstRecord\Elastic;

use App\Entity\Sensor\ConstantRecording\ConstAnalog;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordAnalogRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
    }

    public function find(): ?ConstAnalog
    {
        return null;
    }

    public function findOneBy(): ?ConstAnalog
    {
        return null;
    }

    #[ArrayShape([ConstAnalog::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([ConstAnalog::class])]
    public function findBy(): array
    {
        return [];
    }
}
