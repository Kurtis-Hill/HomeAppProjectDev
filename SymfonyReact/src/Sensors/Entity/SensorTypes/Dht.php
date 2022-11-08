<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Repository\SensorType\ORM\DhtRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: DhtRepository::class),
    ORM\Table(name: "dhtsensor"),
    ORM\UniqueConstraint(name: "cardviewID", columns: ["sensorNameID"]),
    ORM\UniqueConstraint(name: "tempID", columns: ["tempID"]),
    ORM\UniqueConstraint(name: "humidID", columns: ["humidID"]),
]
class Dht implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface
{
    public const NAME = 'Dht';

    public const ALIAS = 'dht';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 80;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -40;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
    ];

    #[
        ORM\Column(name: "dhtID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $dhtID;

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
        ORM\JoinColumn(name: "sensorNameID", referencedColumnName: "sensorNameID"),
    ]
    private Sensor $sensorNameID;

    public function getSensorTypeID(): int
    {
        return $this->dhtID;
    }

    public function setSensorTypeID(int $dhtID): void
    {
        $this->dhtID = $dhtID;
    }

    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorObject(Sensor $sensor): void
    {
        $this->sensorNameID = $sensor;
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

    public function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public static function getSensorTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
