<?php

namespace App\ESPDeviceSensor\Entity\OutOfRangeRecordings;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="outofrangeanalog", indexes={@ORM\Index(name="sensorID", columns={"analogID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsAnalogRepository")
 */
class OutOfRangeAnalog implements OutOfBoundsEntityInterface
{
    /**
     * @ORM\Column(name="outofrangeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $outOfRangeID;

    /**
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false, options={"default"="NULL"})
     */
    #[SoilConstraint(groups: [Soil::NAME])]
    private float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'Out of range analog date time name should not be blank')]
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="sensorNameID")
     * })
     */
    #[Assert\NotNull(message: "Out of range Analog Object cannot be null")]
    private Analog $sensorReadingTypeID;

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

    public function getSensorReadingTypeID(): Analog
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeID(StandardReadingSensorInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Analog) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
