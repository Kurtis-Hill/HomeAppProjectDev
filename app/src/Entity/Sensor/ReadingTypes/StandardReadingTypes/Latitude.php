<?php

namespace App\Entity\Sensor\ReadingTypes\StandardReadingTypes;

use App\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use App\Entity\Sensor\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Repository\Sensor\ReadingType\ORM\LatitudeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: LatitudeRepository::class),
//    ORM\Table(name: "latitude"),
//    ORM\UniqueConstraint(name: "lat_ibfk_1", columns: ["sensorID"]),
]
class Latitude extends AbstractStandardReadingType implements ReadingSymbolInterface
{
    public const READING_TYPE = 'latitude';

    public const HIGH_READING = 90;

    public const LOW_READING = -90;

    public const READING_SYMBOL = '°';

//    #[
//        ORM\Column(name: "latitudeID", type: "integer", nullable: false),
//        ORM\Id,
//        ORM\GeneratedValue(strategy: "NONE"),
//    ]
//    private int $latitudeID;

//    #[ORM\Column(name: "latitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    protected float $currentReading;

//    #[ORM\Column(name: "highLatitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    protected float $highReading = 90;

//    #[ORM\Column(name: "lowLatitude", type: "float", nullable: false),]
    #[LatitudeConstraint]
    protected float $lowReading = -90;

//    #[ORM\Column(name: "constRecord", type: "boolean", nullable: false, options: ["default" => "0"]),]
//    #[Assert\Type("bool")]
//    private bool $constRecord = false;

//    #[
//        ORM\ManyToOne(targetEntity: Sensor::class),
//        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
//    ]
//    private Sensor $sensor;

//    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]),]
//    #[Assert\NotBlank(message: 'Latitude date time should not be blank')]
//    private DateTimeInterface $updatedAt;

    public function getSensorID(): int
    {
        return $this->getReadingTypeID();
    }

//    public function setSensorID(int $latitudeId): void
//    {
//        $this->readingTypeID = $latitudeId;
//    }

//    /**
//     * Sensor relational Objects
//     */
//    public function getSensor(): Sensor
//    {
//        return $this->sensor;
//    }

//    public function setSensor(Sensor $id): void
//    {
//        $this->sensor = $id;
//    }
//
//    public function getCurrentReading(): int
//    {
//        return $this->currentReading;
//    }
//
//    public function getHighReading(): int
//    {
//        return $this->highReading;
//    }
//
//    public function getLowReading(): int
//    {
//        return $this->lowReading;
//    }
//
//    public function getUpdatedAt(): DateTimeInterface
//    {
//        return $this->updatedAt;
//    }
//
//    public function setCurrentReading(int|float|string|bool $reading): void
//    {
//        $this->currentReading = $reading;
//    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highReading = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowReading = $reading;
        }
    }

//    public function setUpdatedAt(): void
//    {
//        $this->updatedAt = new DateTimeImmutable('now');
//    }
//
//    public function getConstRecord(): bool
//    {
//        return $this->constRecord;
//    }
//
//    public function setConstRecord(bool $constRecord): void
//    {
//        $this->constRecord = $constRecord;
//    }

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
