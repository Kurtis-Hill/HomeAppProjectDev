<?php

namespace App\Entity\Sensor\ReadingTypes\LEDReadingTypes;

use App\Repository\Sensor\SensorType\ORM\WS2812BRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WS2812BRepository::class)]
class WS2812B extends AbstractLEDSensorType
{
    public const READING_TYPE = 'WS2812B';

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }
}
