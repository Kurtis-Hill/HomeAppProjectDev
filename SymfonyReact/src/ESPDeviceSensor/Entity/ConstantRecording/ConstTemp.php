<?php

namespace App\ESPDeviceSensor\Entity\ConstantRecording;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstTemp
 *
 * @ORM\Table(name="consttemp", indexes={@ORM\Index(name="consttemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstTemp implements ConstantlyRecordInterface
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
     * @var DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTime $time;

    /**
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $sensorReadingTypeID;

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
     * @return Sensor
     */
    public function getSensorReadingTypeID(): Temperature
    {
        return $this->sensorReadingTypeID;
    }

    /**
     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Temperature) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
