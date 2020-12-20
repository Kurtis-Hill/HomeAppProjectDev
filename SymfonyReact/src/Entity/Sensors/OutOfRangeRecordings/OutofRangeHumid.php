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
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorID", referencedColumnName="sensorNameID")
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
     * @return Sensornames
     */
    public function getSensorid(): Sensornames
    {
        return $this->sensorid;
    }

    /**
     * @param Sensornames $sensorid
     */
    public function setSensorid(Sensornames $sensorid): void
    {
        $this->sensorid = $sensorid;
    }


}
