<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\Temp;
use Doctrine\ORM\Mapping as ORM;

/**
 * Consttemp
 *
 * @ORM\Table(name="consttemp", indexes={@ORM\Index(name="consttemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity
 */
class Consttemp
{
    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tempid;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $sensorreading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $timez = 'current_timestamp()';

    /**
     * @var Temp
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="tempID")
     * })
     */
    private $sensorid;

    /**
     * @return int
     */
    public function getTempid(): int
    {
        return $this->tempid;
    }

    /**
     * @param int $tempid
     */
    public function setTempid(int $tempid): void
    {
        $this->tempid = $tempid;
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
    public function getTimez()
    {
        return $this->timez;
    }

    /**
     * @param \DateTime $timez
     */
    public function setTimez($timez): void
    {
        $this->timez = $timez;
    }

    /**
     * @return Sensors
     */
    public function getSensorid(): Temp
    {
        return $this->sensorid;
    }

    /**
     * @param Sensors $sensorid
     */
    public function setSensorid(Temp $sensorid): void
    {
        $this->sensorid = $sensorid;
    }


}
