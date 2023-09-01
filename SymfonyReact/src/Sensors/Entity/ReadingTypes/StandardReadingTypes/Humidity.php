<?php

namespace App\Sensors\Entity\ReadingTypes\StandardReadingTypes;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Repository\ReadingType\ORM\HumidityRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: HumidityRepository::class),
    ORM\Table(name: "humidity"),
    ORM\UniqueConstraint(name: "humid_ibfk_1", columns: ["sensorID"]),
]
class Humidity extends AbstractStandardReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface, ReadingSymbolInterface
{
    public const READING_TYPE = 'humidity';

    public const READING_SYMBOL = '%';

    public const HIGH_READING = 100;

    public const LOW_READING = 0;

    #[
        ORM\Column(name: "humidID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $humidID;

    #[ORM\Column(name: "humidReading", type: "float", precision: 10, scale: 0, nullable: false)]
    #[HumidityConstraint]
    private float $currentReading;

    #[ORM\Column(name: "highHumid", type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "70"])]
    #[HumidityConstraint]
    private float $highHumid = 80;

    #[ORM\Column(name: "lowHumid", type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "15"])]
    #[HumidityConstraint]
    private float $lowHumid = 10;

    #[ORM\Column(name: "constRecord", type: "boolean", nullable: false, options: ["default" => "0"])]
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    #[Assert\NotBlank(message: 'humidity date time should not be blank')]
    private DateTimeInterface $updatedAt;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;


    public function getSensorID(): int
    {
        return $this->humidID;
    }

    public function setSensorID(int $id): void
    {
        $this->humidID = $id;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $id): void
    {
        $this->sensor = $id;
    }

    public function getCurrentReading(): int|float
    {
        return $this->currentReading;
    }

    public function getHighReading(): int|float
    {
        return $this->highHumid;
    }

    public function getLowReading(): int|float
    {
        return $this->lowHumid;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setCurrentReading(int|float|string|bool $reading): void
    {
        $this->currentReading = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highHumid = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowHumid = $reading;
        }
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    public static function getReadingSymbol(): string
    {
        return self::READING_SYMBOL;
    }
}
