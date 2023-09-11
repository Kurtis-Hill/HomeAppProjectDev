<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Repository\SensorType\ORM\BmpRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: BmpRepository::class),
    ORM\Table(name: "bmp"),
    ORM\UniqueConstraint(name: "humidID", columns: ["humidID"]),
    ORM\UniqueConstraint(name: "latitudeID", columns: ["latitudeID"]),
    ORM\UniqueConstraint(name: "tempID", columns: ["tempID"]),
    ORM\UniqueConstraint(name: "sensorID", columns: ["sensorID"]),

]
class Bmp implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureReadingTypeInterface, HumidityReadingTypeInterface, LatitudeReadingTypeInterface
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
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

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

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getTemperature(): Temperature
    {
        return $this->tempID;
    }

    public function setTemperature(Temperature $tempID): void
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
