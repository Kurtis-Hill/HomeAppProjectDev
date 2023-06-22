<?php

namespace App\Sensors\Entity\ReadingTypes\StandardReadingTypes;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Repository\ReadingType\ORM\LatitudeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: LatitudeRepository::class),
    ORM\Table(name: "latitude"),
    ORM\UniqueConstraint(name: "lat_ibfk_1", columns: ["sensorID"]),
]
class Latitude extends AbstractStandardReadingType implements AllSensorReadingTypeInterface, StandardReadingSensorInterface, ReadingSymbolInterface
{
    public const READING_TYPE = 'latitude';

    public const HIGH_READING = 90;

    public const LOW_READING = -90;

    public const READING_SYMBOL = '°';

    #[
        ORM\Column(name: "latitudeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $latitudeID;

    #[ORM\Column(name: "latitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    private int|float $latitude;

    #[ORM\Column(name: "highLatitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    private int|float $highLatitude = 90;

    #[ORM\Column(name: "lowLatitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    private int|float $lowLatitude = -90;

    #[ORM\Column(name: "constRecord", type: "boolean", nullable: false, options: ["default" => "0"]),]
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]),]
    #[Assert\NotBlank(message: 'Latitude date time should not be blank')]
    private DateTimeInterface $updatedAt;

    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    public function setSensorID(int $latitudeId): void
    {
        $this->latitudeID = $latitudeId;
    }

    /**
     * Sensor relational Objects
     */

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $id): void
    {
        $this->sensor = $id;
    }

    public function getCurrentReading(): int
    {
        return $this->latitude;
    }

    public function getHighReading(): int
    {
        return $this->highLatitude;
    }

    public function getLowReading(): int
    {
        return $this->lowLatitude;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setCurrentReading(int|float|string|bool $reading): void
    {
        $this->latitude = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highLatitude = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowLatitude = $reading;
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
