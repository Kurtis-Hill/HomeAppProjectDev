<?php

namespace App\ESPDeviceSensor\Entity\OutOfRangeRecordings;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * OutOfRangeTemp.
 *
 * @ORM\Table(name="outofrangetemp", indexes={@ORM\Index(name="outofrangetemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutOfRangeTemp implements OutOfBoundsEntityInterface
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
     * @return int
     */
    public function getSensorReading(): int
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
     * @return Temperature
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
