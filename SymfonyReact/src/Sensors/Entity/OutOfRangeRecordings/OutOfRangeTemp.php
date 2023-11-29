<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SHTTemperatureConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsTempRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: OutOfBoundsTempRepository::class),
    ORM\Table(name: "outofrangetemp"),
    ORM\Index(columns: ["tempID"], name: "outofrangetemp_ibfk_1"),
]
class OutOfRangeTemp implements OutOfBoundsEntityInterface
{
    #[
        ORM\Column(name: "outofrangeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $outOfRangeID;

    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false),]
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups: [Sht::NAME]
        )
    ]
    private float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false,
//        options: ["default" => "current_timestamp()"]
    )]
    #[Assert\NotBlank(message: 'out of range temp date time should not be blank')]
    private DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(targetEntity: Temperature::class),
        ORM\JoinColumn(name: "tempID", referencedColumnName: "readingTypeID"),
    ]
    #[Assert\NotNull(message: "Out of range Temperature Object cannot be null")]
    private Temperature $sensorReadingID;

    public function getOutOfRangeID(): int
    {
        return $this->outOfRangeID;
    }

    public function setOutOfRangeID(int $outOfRangeID): void
    {
        $this->outOfRangeID = $outOfRangeID;
    }

    public function getSensorReading(): int
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

    public function getSensorReadingID(): Temperature
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Temperature) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
    }
}
