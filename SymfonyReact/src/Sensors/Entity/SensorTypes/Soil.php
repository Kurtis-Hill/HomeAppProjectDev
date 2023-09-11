<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Repository\SensorType\ORM\SoilRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: SoilRepository::class),
    ORM\Table(name: "soil"),
    ORM\UniqueConstraint(name: "analogID", columns: ["analogID"]),
    ORM\UniqueConstraint(name: "sensorID", columns: ["sensorID"]),
]
class Soil implements SensorTypeInterface, StandardSensorTypeInterface, AnalogReadingTypeInterface
{
    public const NAME = 'Soil';

    public const ALIAS = 'soil';

    public const HIGH_SOIL_READING_BOUNDARY = 9999;

    public const LOW_SOIL_READING_BOUNDARY = 1000;

    private const ALLOWED_READING_TYPES = [
        Analog::READING_TYPE
    ];

    #[
        ORM\Column(name: "soilID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $soilID;

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

    public function getSensorTypeID(): int
    {
        return $this->soilID;
    }

    public function setSensorTypeID(int $soilID): void
    {
        $this->soilID = $soilID;
    }

    public function getAnalogObject(): Analog
    {
        return $this->analogID;
    }

    public function setAnalogObject(Analog $analogID): void
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

    public function getMaxAnalog(): float|int
    {
        return self::HIGH_SOIL_READING_BOUNDARY;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_SOIL_READING_BOUNDARY;
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
}
