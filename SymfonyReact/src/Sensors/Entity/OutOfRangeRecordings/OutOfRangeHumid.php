<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Repository\ORM\OutOfBounds\OutOfBoundsHumidityRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: OutOfBoundsHumidityRepository::class),
    ORM\Table(name: "outofrangehumid"),
    ORM\Index(columns: ["humidID"], name: "humidID"),
]
class OutOfRangeHumid implements OutOfBoundsEntityInterface
{
    #[
        ORM\Column(name: "outofrangeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $outOfRangeID;

    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "NULL"]),]
    #[HumidityConstraint]
    private float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    #[Assert\NotBlank(message: 'Out of range humidity date time should not be blank')]
    private DateTime $createdAt;


    #[
        ORM\ManyToOne(targetEntity: Humidity::class),
        ORM\JoinColumn(name: "humidID", referencedColumnName: "humidID"),
    ]
    #[Assert\NotNull(message: "Out of range Humidity Object cannot be null")]
    private Humidity $sensorReadingTypeID;

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

    public function getSensorReadingTypeID(): Humidity
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Humidity) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
