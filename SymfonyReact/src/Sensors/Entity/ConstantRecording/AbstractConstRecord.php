<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;

#[
    Entity,
    ORM\Table(name: "standardreadingtype"),
    ORM\Index(columns: ["sensorReading"], name: "sensorReading"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    InheritanceType('SINGLE_TABLE'),
    DiscriminatorColumn(name: 'sensorReadingType', type: 'string'),
    DiscriminatorMap(
        [
            Temperature::READING_TYPE => ConstTemp::class,
            Humidity::READING_TYPE => ConstHumid::class,
            Analog::READING_TYPE => ConstAnalog::class,
            Latitude::READING_TYPE => ConstLatitude::class,
//            Motion::READING_TYPE => ConstMotion::class,
//            Relay::READING_TYPE => ConstRelay::class,
        ]
    )
]
abstract class AbstractConstRecord
{
    #[
        ORM\Column(name: "constRecordID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $constRecordID;

    #[
        ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false),
    ]
    protected float $sensorReading;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false),
    ]
    #[Assert\NotBlank(message: 'Const analog date time should not be blank')]
    protected DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseSensorReadingType", referencedColumnName: "baseReadingTypeID"),
    ]
    #[Assert\NotNull(message: "Const Record Analog Object cannot be null")]
    protected BaseSensorReadingType $sensorReadingID;

    public function getConstRecordID(): int
    {
        return $this->constRecordID;
    }

    public function setConstRecordID(int $constRecordID): void
    {
        $this->constRecordID = $constRecordID;
    }

    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    public function setSensorReading(float $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getSensorReadingObject(): BaseSensorReadingType
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof BaseSensorReadingType) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
    }
}
