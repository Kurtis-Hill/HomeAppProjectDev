<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;

use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordAnalogRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public function flush(): void
    {
        // TODO: Implement flush() method.
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
