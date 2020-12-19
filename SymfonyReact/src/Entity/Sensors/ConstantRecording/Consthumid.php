<?php

namespace App\Entity\Sensors\ConstantRecording;

use App\Entity\Sensors\Humid;
use Doctrine\ORM\Mapping as ORM;

/**
 * Consthumid
 *
 * @ORM\Table(name="consthumid", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class Consthumid
{
    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $humidid;

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
     * @var Humid
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="humidID")
     * })
     */
    private $sensorid;

    /**
     * @return int
     */
    public function getHumidid(): int
    {
        return $this->humidid;
    }

    /**
     * @param int $humidid
     */
    public function setHumidid(int $humidid): void
    {
        $this->humidid = $humidid;
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
     * @return Humid
     */
    public function getSensorid(): Humid
    {
        return $this->sensorid;
    }

    /**
     * @param Humid $sensorid
     */
    public function setSensorid(Humid $sensorid): void
    {
        $this->sensorid = $sensorid;
    }


}
