<?php

namespace App\ESPDeviceSensor\Entity\ConstantRecording;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstHumid
 *
 * @ORM\Table(name="consthumid", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstHumid implements ConstantlyRecordInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="constRecordID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $constRecordID;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private float $sensorReading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $time;

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
    public function getConstRecordID(): int
    {
        return $this->constRecordID;
    }

    /**
     * @param int $constRecordID
     */
    public function setConstRecordID(int $constRecordID): void
    {
        $this->constRecordID = $constRecordID;
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
     * @return \DateTime
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
