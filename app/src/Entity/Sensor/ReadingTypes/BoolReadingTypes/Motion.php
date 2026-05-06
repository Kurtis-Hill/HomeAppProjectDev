<?php

namespace App\Entity\Sensor\ReadingTypes\BoolReadingTypes;

use App\Repository\Sensor\ReadingType\ORM\MotionRepository;
use Doctrine\ORM\Mapping\Entity;

#[
    Entity(repositoryClass: MotionRepository::class),
]
class Motion extends AbstractBoolReadingBaseSensor
{
    public const READING_TYPE = 'motion';

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }
}
