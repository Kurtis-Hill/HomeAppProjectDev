<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\ReadingTypes\Humid;
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
    private $humidID;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $sensorReading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $time = 'current_timestamp()';

    /**
     * @var Humid
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="humidID")
     * })
     */
    private $sensorID;

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
     * @return Humid
     */
    public function getSensorID(): Humid
    {
        return $this->sensorID;
    }

    /**
     * @param Humid $sensorID
     */
    public function setSensorID(Humid $sensorID): void
    {
        $this->sensorID = $sensorID;
    }


}
