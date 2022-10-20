<?php

namespace App\Sensors\Repository\ConstRecord\Elastic;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\Sensors\Entity\ConstantRecording\ConstLatitude;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

class ConstRecordLatitudeRepository extends AbstractConstRecordRepository implements ConstantlyRecordRepositoryInterface
{
    public const ES_INDEX = 'constrecord_latitude';

    public function persist(ConstantlyRecordInterface $sensorReadingData): void
    {
        // TODO: Implement persist() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
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
