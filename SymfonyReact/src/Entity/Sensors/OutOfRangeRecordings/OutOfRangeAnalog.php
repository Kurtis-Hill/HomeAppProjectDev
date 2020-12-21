<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;

use App\Entity\Sensors\ReadingTypes\Analog;
use Doctrine\ORM\Mapping as ORM;

/**
 * OutOfRangeAnalog
 *
 * @ORM\Table(name="outofrangeanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class OutOfRangeAnalog
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $analogID;

    /**
     * @var float|null
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $sensorReading = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $time = 'current_timestamp()';

    /**
     * @var Analog
     *
     * @ORM\ManyToOne(targetEntity="Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="analogID")
     * })
     */
    private Analog $sensorID;

    /**
     * @return int
     */
    public function getAnalogID(): int
    {
        return $this->analogID;
    }

    /**
     * @param int $analogID
     */
    public function setAnalogID(int $analogID): void
    {
        $this->analogID = $analogID;
    }

    /**
     * @return float|null
     */
    public function getSensorReading()
    {
        return $this->sensorReading;
    }

    /**
     * @param float|null $sensorReading
     */
    public function setSensorReading($sensorReading): void
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
     * @return Analog
     */
    public function getSensorID(): Analog
    {
        return $this->sensorID;
    }

    /**
     * @param Analog $sensorID
     */
    public function setSensorID(Analog $sensorID): void
    {
        $this->sensorID = $sensorID;
    }


}
