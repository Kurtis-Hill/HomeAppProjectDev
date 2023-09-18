<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Repository\SensorType\ORM\LDRRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: LDRRepository::class),
    ORM\Table(name: "ldr"),
    ORM\UniqueConstraint(name: "analogID", columns: ["analogID"]),
    ORM\UniqueConstraint(name: "sensorID", columns: ["sensorID"]),
]
class LDR implements SensorTypeInterface, StandardSensorTypeInterface, AnalogReadingTypeInterface
{
    public const NAME = 'Ldr';

    public const ALIAS = 'ldr';

    public const HIGH_READING = 999;

    public const LOW_READING = 100;

    private const ALLOWED_READING_TYPES = [
        Analog::READING_TYPE
    ];

    #[
        ORM\Column(name: "ldrID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $ldrID;

    #[
        ORM\ManyToOne(targetEntity: Analog::class),
        ORM\JoinColumn(name: "analogID", referencedColumnName: "analogID"),
    ]
    private Analog $analogID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID", nullable: true),
    ]
    private Sensor $sensor;

    public function getLdrID(): int
    {
        return $this->ldrID;
    }

    public function setLdrID(int $ldrID): void
    {
        $this->ldrID = $ldrID;
    }

    public function getSensorTypeID(): int
    {
        return $this->ldrID;
    }

    public function setSensorTypeID(int $ldrID): void
    {
        $this->ldrID = $ldrID;
    }

    public function getAnalogID(): Analog
    {
        return $this->analogID;
    }

    public function setAnalogID(Analog $analogID): void
    {
        $this->analogID = $analogID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }
    public function getAnalogObject() : Analog
    {
        return $this->analogID;
    }

    public function setAnalogObject(Analog $analogID) : void
    {
        $this->analogID = $analogID;
    }

    public static function getReadingTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }

    public function getMaxAnalog(): float|int
    {
        return self::HIGH_READING;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_READING;
    }

    public function getReadingTypeName(): string
    {
        return self::NAME;
    }
}
