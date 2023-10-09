<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\ShtRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ShtRepository::class),
    ORM\Table(name: "sht"),
    ORM\UniqueConstraint(name: "sensorID", columns: ["sensorID"]),
    ORM\UniqueConstraint(name: "tempID", columns: ["tempID"]),
    ORM\UniqueConstraint(name: "humidID", columns: ["humidID"]),
]
class Sht implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureReadingTypeInterface, HumidityReadingTypeInterface
{
    public const NAME = 'Sht';

    public const ALIAS = 'sht';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 125;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -40;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
    ];

    #[
        ORM\Column(name: "shtID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $shtID;

    #[
        ORM\ManyToOne(targetEntity: Temperature::class),
        ORM\JoinColumn(name: "tempID", referencedColumnName: "tempID"),
    ]
    private Temperature $tempID;

    #[
        ORM\ManyToOne(targetEntity: Humidity::class),
        ORM\JoinColumn(name: "humidID", referencedColumnName: "humidID"),
    ]
    private Humidity $humidID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    public function getSensorTypeID(): int
    {
        return $this->shtID;
    }

    public function setSensorTypeID(int $dhtID): void
    {
        $this->shtID = $dhtID;
    }

    public function getTemperature(): Temperature
    {
        return $this->tempID;
    }

    public function setTemperature(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getHumidObject(): Humidity
    {
        return $this->humidID;
    }

    public function setHumidObject(Humidity $humidID): void
    {
        $this->humidID = $humidID;
    }

    public function getMaxTemperature(): float|int
    {
        return self::HIGH_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMinTemperature(): float|int
    {
        return self::LOW_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMaxHumidity(): float|int
    {
        return Humidity::HIGH_READING;
    }

    public function getMinHumidity(): float|int
    {
        return Humidity::LOW_READING;
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
