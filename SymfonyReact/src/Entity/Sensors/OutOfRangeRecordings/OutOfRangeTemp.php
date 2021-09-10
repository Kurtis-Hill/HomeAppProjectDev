<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;


use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use Doctrine\ORM\Mapping as ORM;

/**
 * OutOfRangeTemp.
 *
 * @ORM\Table(name="outofrangetemp", indexes={@ORM\Index(name="outofrangetemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutOfRangeTemp
{
    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $tempID;

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
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $sensorID;

    /**
     * @return int
     */
    public function getTempID(): int
    {
        return $this->tempID;
    }

    /**
     * @param int $tempID
     */
    public function setTempID(int $tempID): void
    {
        $this->tempID = $tempID;
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
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(?\DateTime $time = null): void
    {
        $this->time = $time ?? new \DateTime('now');
    }

    /**
     * @return Sensors
     */
    public function getSensorID(): Sensors
    {
        return $this->sensorID;
    }

    /**
     * @param Temperature $sensorID
     */
    public function setSensorID(Temperature $sensorID): void
    {
        $this->sensorID = $sensorID;
    }
}
