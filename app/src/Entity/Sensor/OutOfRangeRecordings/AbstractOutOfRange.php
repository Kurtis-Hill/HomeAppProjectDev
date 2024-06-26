<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;

#[
    Entity,
    ORM\Table(name: "readingtypeoutofrange"),
    ORM\Index(columns: ["sensorReading"], name: "sensorReading"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    InheritanceType('SINGLE_TABLE'),
    DiscriminatorColumn(name: 'sensorReadingType', type: 'string'),
    DiscriminatorMap(
        [
            Temperature::READING_TYPE => OutOfRangeTemp::class,
            Humidity::READING_TYPE => OutOfRangeHumid::class,
            Analog::READING_TYPE => OutOfRangeAnalog::class,
            Latitude::READING_TYPE => OutOfRangeLatitude::class,
//            Motion::READING_TYPE => ConstMotion::class,
//            Relay::READING_TYPE => ConstRelay::class,
        ]
    )
]
abstract class AbstractOutOfRange implements OutOfBoundsEntityInterface
{
    #[
        ORM\Column(name: "outofrangeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $outOfRangeID;

    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "NULL"]),]
    private float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    #[Assert\NotBlank(message: 'Out of range humidity date time should not be blank')]
    private DateTime $createdAt;

    #[
        ORM\ManyToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseReadingTypeID", referencedColumnName: "baseReadingTypeID"),
    ]
    #[Assert\NotNull(message: "Out of range base sensor cannot be null")]
    private BaseSensorReadingType $baseSensorReadingType;

    public function getOutOfRangeID(): int
    {
        return $this->outOfRangeID;
    }

    public function setOutOfRangeID(int $outOfRangeID): void
    {
        $this->outOfRangeID = $outOfRangeID;
    }

    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    public function setSensorReading(float $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime('now');
    }

    public function getBaseSensorReadingType(): BaseSensorReadingType
    {
        return $this->baseSensorReadingType;
    }

    public function setBaseSensorReadingType(BaseSensorReadingType $sensorReadingTypeID): void
    {
        $this->baseSensorReadingType = $sensorReadingTypeID;
    }
}
