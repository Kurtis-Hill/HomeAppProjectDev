<?php

namespace App\Repository\Sensor\ConstRecord\Elastic;

use App\Entity\Sensor\ConstantRecording\ConstHumid;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordHumidityRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
    }

    public function find(): ?ConstHumid
    {
        return null;
    }

    public function findOneBy(): ?ConstHumid
    {
        return null;
    }

    #[ArrayShape([ConstHumid::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([ConstHumid::class])]
    public function findBy(): array
    {
        return [];
    }
}
