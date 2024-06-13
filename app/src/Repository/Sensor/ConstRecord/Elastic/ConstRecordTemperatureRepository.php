<?php

namespace App\Repository\Sensor\ConstRecord\Elastic;

use App\Entity\Sensor\ConstantRecording\ConstTemp;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordTemperatureRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
        $this->_em->flush();
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
