<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Repository\Sensor\SensorType\ORM\GenericRelayRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: GenericRelayRepository::class),
]
class GenericRelay extends AbstractSensorType implements RelayReadingTypeInterface, BoolSensorTypeInterface
{
    public const NAME = 'GenericRelay';

    public const ALIAS = 'generic_relay';

    public const ALLOWED_READING_TYPES = [
        Relay::READING_TYPE,
    ];

    public static function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public static function getReadingTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
