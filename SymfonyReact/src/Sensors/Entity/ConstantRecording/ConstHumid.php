<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryHumidRepository;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordRepositoryHumidRepository::class),
    ORM\Table(name: "consthumid"),
    ORM\Index(columns: ["humidID"], name: "humidID"),
]
class ConstHumid implements ConstantlyRecordInterface
{
    #[
        ORM\Column(name: "constRecordID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $constRecordID;

    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false), ]
    #[HumidityConstraint]
    private float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]), ]
    #[Assert\NotBlank(message: 'Const humidity date time should not be blank')]
    private DateTimeImmutable $createdAt;

    #[
        ORM\ManyToOne(targetEntity: Humidity::class),
        ORM\JoinColumn(name: "humidID", referencedColumnName: "humidID"),
    ]
    #[Assert\NotNull(message: "Const Record Humidity Object cannot be null")]
    private Humidity $sensorReadingTypeID;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getSensorReadingTypeObject(): Humidity
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Humidity) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
