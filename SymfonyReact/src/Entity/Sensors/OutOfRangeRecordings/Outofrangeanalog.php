<?php

namespace App\Entity\Sensors\OutOfRangeRecordings;

use App\Entity\Sensors\Analog;
use Doctrine\ORM\Mapping as ORM;

/**
 * Outofrangeanalog
 *
 * @ORM\Table(name="outofrangeanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class Outofrangeanalog
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
     * @var float|null
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $sensorreading = 'NULL';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $timez = 'current_timestamp()';

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
     * @return float|null
     */
    public function getSensorreading()
    {
        return $this->sensorreading;
    }

    /**
     * @param float|null $sensorreading
     */
    public function setSensorreading($sensorreading): void
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
