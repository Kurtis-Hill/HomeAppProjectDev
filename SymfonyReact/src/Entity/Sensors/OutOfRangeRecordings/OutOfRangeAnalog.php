<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;

use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
/**
 * OutOfRangeAnalog.
 *
 * @ORM\Table(name="outofrangeanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutOfRangeAnalog implements OutOfBoundsEntityInterface
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
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private float $sensorReading;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTime $time;

    /**
     * @var Analog
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="sensorNameID")
     * })
     */
    private Analog $sensorReadingTypeID;

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
     * @return AllSensorReadingTypeInterface
     */
    public function getSensorReadingTypeID(): AllSensorReadingTypeInterface
    {
        return $this->sensorReadingTypeID;
    }

    /**
     * @param AllSensorReadingTypeInterface $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        $this->sensorReadingTypeID = $sensorReadingTypeID;
    }
}
