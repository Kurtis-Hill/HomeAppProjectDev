<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;

use App\Entity\Sensors\Sensors;
use Doctrine\ORM\Mapping as ORM;

/**
 * OutofRangeHumid
 *
 * @ORM\Table(name="outofrangehumid", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutofRangeHumid
{
    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $humidID;

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
    private $time = 'current_timestamp()';

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorID;

    /**
     * @return int
     */
    public function getHumidID(): int
    {
        return $this->humidID;
    }

    /**
     * @param int $humidID
     */
    public function setHumidID(int $humidID): void
    {
        $this->humidID = $humidID;
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
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return Sensors
     */
    public function getSensorID(): Sensors
    {
        return $this->sensorID;
    }

    /**
     * @param Sensors $sensorID
     */
    public function setSensorID(Sensors $sensorID): void
    {
        $this->sensorID = $sensorID;
    }
}
