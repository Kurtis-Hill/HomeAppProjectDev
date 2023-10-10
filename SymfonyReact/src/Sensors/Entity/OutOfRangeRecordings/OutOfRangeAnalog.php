<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsAnalogRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: OutOfBoundsAnalogRepository::class),
    ORM\Table(name: "outofrangeanalog"),
    ORM\Index(columns: ["analogID"], name: "analogID"),
]
class OutOfRangeAnalog implements OutOfBoundsEntityInterface
{
    #[
        ORM\Column(name: "outofrangeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $outOfRangeID;

    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "NULL"])]
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    private float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    #[Assert\NotBlank(message: 'Out of range analog date time name should not be blank')]
    private DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(targetEntity: Analog::class),
        ORM\JoinColumn(name: "analogID", referencedColumnName: "analogID"),
    ]
    #[Assert\NotNull(message: "Out of range Analog Object cannot be null")]
    private Analog $sensorReadingID;

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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getSensorReadingID(): Analog
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Analog) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
    }
}
