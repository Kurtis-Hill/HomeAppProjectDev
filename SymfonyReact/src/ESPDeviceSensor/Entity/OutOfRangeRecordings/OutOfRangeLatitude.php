<?php

namespace App\ESPDeviceSensor\Entity\OutOfRangeRecordings;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="outofrangelatitude")
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsLatitudeRepository")
 */
class OutOfRangeLatitude implements OutOfBoundsEntityInterface
{
    /**
     * @ORM\Column(name="outofrangeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $outOfRangeID;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Latitude")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="latitudeID", referencedColumnName="latitudeID")
     * })
     */
    private Latitude $sensorReadingTypeID;

    /**
     * @ORM\Column(name="sensorReading", type="integer", nullable=false)
     */
    #[LatitudeConstraint]
    private int|float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTimeInterface $createdAt;

    public function getOutOfRangeID(): int
    {
        return $this->outOfRangeID;
    }

    public function setOutOfRangeID(int $outOfRangeID): void
    {
        $this->outOfRangeID = $outOfRangeID;
    }

    public function getSensorReadingTypeID(): Latitude
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Latitude) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
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
