<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordRepositoryLatitudeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordRepositoryLatitudeRepository::class),
    ORM\Table(name: "constlatitude"),
    ORM\Index(columns: ["latitudeID"], name: "latitudeID"),
]
class ConstLatitude implements ConstantlyRecordInterface
{
    #[
        ORM\Column(name: "constRecordID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $constRecordID;

    #[ORM\Column(name: "sensorReading", type: "integer", nullable: false), ]
    #[LatitudeConstraint]
    private int|float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]), ]
    #[Assert\NotBlank(message: 'Const latitude date time should not be blank')]
    private DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(targetEntity: Latitude::class),
        ORM\JoinColumn(name: "latitudeID", referencedColumnName: "latitudeID"),
    ]
    #[Assert\NotNull(message: "Const Record Latitude Object cannot be null")]
    private Latitude $sensorReadingID;

    public function getConstRecordID(): int
    {
        return $this->constRecordID;
    }

    public function setConstRecordID(int $constRecordID): void
    {
        $this->constRecordID = $constRecordID;
    }

    public function getSensorReading(): float|int
    {
        return $this->sensorReading;
    }

    public function setSensorReading(float|int $sensorReading): void
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

    public function getSensorReadingObject(): Latitude
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Latitude) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
    }
}
