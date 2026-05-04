<?php

namespace App\Repository\Sensor\ConstRecord\Elastic;

use App\Entity\Sensor\ConstantRecording\ConstLatitude;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordLatitudeRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function find(): ?ConstLatitude
    {
        return null;
    }

    public function findOneBy(): ?ConstLatitude
    {
        return null;
    }

    #[ArrayShape([ConstLatitude::class])]
    public function findAll(): array
    {
        return [];
    }

    #[ArrayShape([ConstLatitude::class])]
    public function findBy(): array
    {
        return [];
    }
}
