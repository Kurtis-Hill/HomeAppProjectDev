<?php

namespace App\ESPDeviceSensor\Entity\OutOfRangeRecordings;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OutOfRangeTemp.
 *
 * @ORM\Table(name="outofrangetemp", indexes={@ORM\Index(name="outofrangetemp_ibfk_1", columns={"tempID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\OutOfBounds\OutOfBoundsTempORMRepository")
 */
class OutOfRangeTemp implements OutOfBoundsEntityInterface
{
    /**
     * @ORM\Column(name="outofrangeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $outOfRangeID;

    /**
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        )
    ]
    private float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'out of range temp date time should not be blank')]
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    #[Assert\NotNull(message: "Out of range Temperature Object cannot be null")]
    private Temperature $sensorReadingTypeID;

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

    public function getSensorReadingTypeID(): Temperature
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Temperature) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
