<?php

namespace App\Entity\Sensors;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensors
 *
 * @ORM\Table(name="sensornames", indexes={@ORM\Index(name="SensorType", columns={"sensorTypeID"}), @ORM\Index(name="sensornames_ibfk_1", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorNamesRepository")
 */
class Sensors
{
    /**
     * @var int
     *
     * @ORM\Column(name="sensorNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sensornameid;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorName", type="string", length=20, nullable=false)
     */
    private $sensorname;

    /**
     * @var \Sensortype
     *
     * @ORM\ManyToOne(targetEntity="Sensortype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorTypeID", referencedColumnName="sensorTypeID")
     * })
     */
    private $sensortypeid;

    /**
     * @var \Devices
     *
     * @ORM\ManyToOne(targetEntity="Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private $devicenameid;

    /**
     * @return int
     */
    public function getSensornameid(): int
    {
        return $this->sensornameid;
    }

    /**
     * @param int $sensornameid
     */
    public function setSensornameid(int $sensornameid): void
    {
        $this->sensornameid = $sensornameid;
    }

    /**
     * @return string
     */
    public function getSensorname(): string
    {
        return $this->sensorname;
    }

    /**
     * @param string $sensorname
     */
    public function setSensorname(string $sensorname): void
    {
        $this->sensorname = $sensorname;
    }

    /**
     * @return \Sensortype
     */
    public function getSensortypeid(): \Sensortype
    {
        return $this->sensortypeid;
    }

    /**
     * @param \Sensortype $sensortypeid
     */
    public function setSensortypeid(\Sensortype $sensortypeid): void
    {
        $this->sensortypeid = $sensortypeid;
    }

    /**
     * @return \Devicenames
     */
    public function getDevicenameid(): \Devicenames
    {
        return $this->devicenameid;
    }

    /**
     * @param \Devicenames $devicenameid
     */
    public function setDevicenameid(\Devicenames $devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }
}
