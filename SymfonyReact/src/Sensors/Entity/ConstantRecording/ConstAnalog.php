<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordAnalogRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordAnalogRepository::class),
    ORM\Table(name: "constanalog"),
    ORM\Index(columns: ["analogID"], name: "analogID"),
]

class ConstAnalog implements ConstantlyRecordEntityInterface
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
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    private float $sensorReading;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false),
    ]
    #[Assert\NotBlank(message: 'Const analog date time should not be blank')]
    private DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(targetEntity: Analog::class),
        ORM\JoinColumn(name: "analogID", referencedColumnName: "analogID"),
    ]
    #[Assert\NotNull(message: "Const Record Analog Object cannot be null")]
    private Analog $sensorReadingID;

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

    public function getSensorReadingObject(): Analog
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Analog) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
    }
}
