<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\ReadingTypes\Humidity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstHumid
 *
 * @ORM\Table(name="consthumid", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstHumid
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
    private $time;

    /**
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="humidID")
     * })
     */
    private Humidity $sensorID;

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
    public function setTime(?\DateTime $time): void
    {
        $this->time = $time === null ?  new \DateTime('now') : $time;
    }

    /**
     * @return Humidity
     */
    public function getSensorID(): Humidity
    {
        return $this->sensorID;
    }

    /**
     * @param Humidity $sensorID
     */
    public function setSensorID(Humidity $sensorID): void
    {
        $this->sensorID = $sensorID;
    }


}
