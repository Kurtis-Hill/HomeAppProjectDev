<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Repository\SensorType\ORM\GenericRelayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: GenericRelayRepository::class),
    ORM\Table(name: "genericrelay"),
]
class GenericRelay implements SensorTypeInterface, RelayReadingTypeInterface, BoolSensorTypeInterface
{
    public const NAME = 'GenericRelay';

    public const ALIAS = 'generic_relay';

    public const ALLOWED_READING_TYPES = [
        Relay::READING_TYPE,
    ];

    #[
        ORM\Column(name: "genericrelayID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $genericRelayID;

    #[
        ORM\ManyToOne(targetEntity: Relay::class),
        ORM\JoinColumn(name: "relayID", referencedColumnName: "boolID"),
    ]
    private Relay $relay;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    public function getGenericRelayID(): int
    {
        return $this->genericRelayID;
    }

    public function getRelay(): Relay
    {
        return $this->relay;
    }

    public function setRelay(Relay $relay): void
    {
        $this->relay = $relay;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getReadingTypeName(): string
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

    public function getSensorTypeID(): int
    {
        return $this->genericRelayID;
    }

    public function getReadingTypes(): Collection
    {
        return new ArrayCollection([$this->relay]);
    }
}
