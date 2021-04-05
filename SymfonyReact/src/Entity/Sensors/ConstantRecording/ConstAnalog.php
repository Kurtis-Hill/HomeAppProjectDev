<?php

namespace App\Entity\Sensors\ConstantRecording;


use App\Entity\Sensors\ReadingTypes\Analog;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstAnalog
 *
 * @ORM\Table(name="constanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstAnalog
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
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private float $sensorReading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="date", nullable=false)
     */
    private \DateTime $time;

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
    public function getTime(): \DateTime
    {
        return $this->time;
    }


    public function setTime(): void
    {
        $this->time = new \DateTime();
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
