<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstHumid;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordHumidityRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
        // TODO: Implement flush() method.
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
