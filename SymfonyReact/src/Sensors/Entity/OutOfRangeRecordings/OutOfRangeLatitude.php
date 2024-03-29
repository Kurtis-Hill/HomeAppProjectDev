<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsLatitudeRepository::class),
    ORM\Table(name: "outofrangelatitude"),
]
class OutOfRangeLatitude implements OutOfBoundsEntityInterface
{
    #[
        ORM\Column(name: "outofrangeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $outOfRangeID;

    #[
        ORM\ManyToOne(targetEntity: Latitude::class),
        ORM\JoinColumn(name: "latitudeID", referencedColumnName: "latitudeID"),
    ]
    private Latitude $sensorReadingID;

    #[ORM\Column(name: "sensorReading", type: "float", nullable: false)]
    #[LatitudeConstraint]
    private int|float $sensorReading;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]),]
    private DateTimeInterface $createdAt;

    public function getOutOfRangeID(): int
    {
        return $this->outOfRangeID;
    }

    public function setOutOfRangeID(int $outOfRangeID): void
    {
        $this->outOfRangeID = $outOfRangeID;
    }

    public function getSensorReadingID(): Latitude
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Latitude) {
            $this->sensorReadingID = $sensorReadingTypeID;
        }
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
}
