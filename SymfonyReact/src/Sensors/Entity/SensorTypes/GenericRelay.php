<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\GenericRelayRepository;
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

    public static function getReadingTypeName(): string
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
