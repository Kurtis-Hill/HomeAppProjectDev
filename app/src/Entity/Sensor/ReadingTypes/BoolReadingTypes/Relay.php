<?php

namespace App\Entity\Sensor\ReadingTypes\BoolReadingTypes;

use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use Doctrine\ORM\Mapping\Entity;

#[Entity(
    repositoryClass: RelayRepository::class
)]
class Relay extends AbstractBoolReadingBaseSensor
{
    public const READING_TYPE = 'relay';

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public function getSensorID(): int
    {
        return $this->boolID;
    }
}
