<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\Analog;
use Doctrine\ORM\Mapping as ORM;

/**
 * Constanalog
 *
 * @ORM\Table(name="constanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class Constanalog
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $analogid;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $sensorreading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="date", nullable=false)
     */
    private $timez;

    /**
     * @var Analog
     *
     * @ORM\ManyToOne(targetEntity="Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="analogID")
     * })
     */
    private $sensorid;

    /**
     * @return int
     */
    public function getAnalogid(): int
    {
        return $this->analogid;
    }

    /**
     * @param int $analogid
     */
    public function setAnalogid(int $analogid): void
    {
        $this->analogid = $analogid;
    }

    /**
     * @return float
     */
    public function getSensorreading(): float
    {
        return $this->sensorreading;
    }

    /**
     * @param float $sensorreading
     */
    public function setSensorreading(float $sensorreading): void
    {
        $this->sensorreading = $sensorreading;
    }

    /**
     * @return \DateTime
     */
    public function getTimez(): \DateTime
    {
        return $this->timez;
    }

    /**
     * @param \DateTime $timez
     */
    public function setTimez(\DateTime $timez): void
    {
        $this->timez = $timez;
    }

    /**
     * @return Analog
     */
    public function getSensorid(): Analog
    {
        return $this->sensorid;
    }

    /**
     * @param Analog $sensorid
     */
    public function setSensorid(Analog $sensorid): void
    {
        $this->sensorid = $sensorid;
    }

}
