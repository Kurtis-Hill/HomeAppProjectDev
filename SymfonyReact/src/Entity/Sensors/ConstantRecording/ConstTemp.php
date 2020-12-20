<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstTemp
 *
 * @ORM\Table(name="consttemp", indexes={@ORM\Index(name="consttemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstTemp
{
    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tempID;

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
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="tempID")
     * })
     */
    private $sensorID;

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
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return Sensors
     */
    public function getSensorID(): Temp
    {
        return $this->sensorID;
    }

    /**
     * @param Sensors $sensorID
     */
    public function setSensorID(Temp $sensorID): void
    {
        $this->sensorID = $sensorID;
    }


}
