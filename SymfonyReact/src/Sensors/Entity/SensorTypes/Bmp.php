<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Repository\ORM\SensorType\BmpRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: BmpRepository::class),
    ORM\Table(name: "bmp"),
    ORM\UniqueConstraint(name: "humidID", columns: ["humidID"]),
    ORM\UniqueConstraint(name: "latitudeID", columns: ["latitudeID"]),
    ORM\UniqueConstraint(name: "tempID*", columns: ["tempID"]),
    ORM\Index(columns: ["sensorNameID"], name: "bmp_ibfk_1"),

]
class Bmp implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface, LatitudeSensorTypeInterface
{
    public const NAME = 'Bmp';

    public const ALIAS = 'bmp';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 85;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -45;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
        Latitude::READING_TYPE
    ];

    #[
        ORM\Column(name: "bmpID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $bmpID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorNameID", referencedColumnName: "sensorNameID"),
    ]
    private Sensor $sensorNameID;

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
        ORM\ManyToOne(targetEntity: Latitude::class),
        ORM\JoinColumn(name: "latitudeID", referencedColumnName: "latitudeID"),
    ]
    private Latitude $latitudeID;

    public function getSensorTypeID(): int
    {
        return $this->bmpID;
    }

    public function setSensorTypeID(int $bmpID): void
    {
        $this->bmpID = $bmpID;
    }

    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorObject(Sensor $sensor): void
    {
        $this->sensorNameID = $sensor;
    }

    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    public function getHumidObject(): Humidity
    {
        return $this->humidID;
    }

    public function setHumidObject(Humidity $humidID): void
    {
        $this->humidID = $humidID;
    }

    public function getLatitudeObject(): Latitude
    {
        return $this->latitudeID;
    }


    public function setLatitudeObject(Latitude $latitudeID): void
    {
        $this->latitudeID = $latitudeID;
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

    public function getMaxLatitude(): float|int
    {
        return Latitude::HIGH_READING;
    }

    public function getMinLatitude(): float|int
    {
        return Latitude::LOW_READING;
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
