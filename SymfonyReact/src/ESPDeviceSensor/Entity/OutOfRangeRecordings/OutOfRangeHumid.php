<?php

namespace App\ESPDeviceSensor\Entity\OutOfRangeRecordings;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * OutofRangeHumid
 *
 * @ORM\Table(name="outofrangehumid", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutOfRangeHumid implements OutOfBoundsEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="outofrangeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $outOfRangeID;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private float $sensorReading;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTime $time;

    /**
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $sensorReadingTypeID;

    /**
     * @return int
     */
    public function getOutOfRangeID(): int
    {
        return $this->outOfRangeID;
    }

    /**
     * @param int $outOfRangeID
     */
    public function setOutOfRangeID(int $outOfRangeID): void
    {
        $this->outOfRangeID = $outOfRangeID;
    }

    /**
     * @return float
     */
    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    /**
     * @param float $sensorReading
     */
    public function setSensorReading(float $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime|null $time
     */
    public function setTime(?DateTime $time = null): void
    {
        $this->time = $time ?? new DateTime('now');
    }

    /**
     * @return Humidity
     */
    public function getSensorReadingTypeID(): Humidity
    {
        return $this->sensorReadingTypeID;
    }

    /**
     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Humidity) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
