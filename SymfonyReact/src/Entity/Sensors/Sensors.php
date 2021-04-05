<?php

namespace App\Entity\Sensors;

use App\Entity\Devices\Devices;
use Doctrine\ORM\Mapping as ORM;

/**
 * Sensors.
 *
 * @ORM\Table(name="sensornames", indexes={@ORM\Index(name="SensorTypes", columns={"sensorTypeID"}), @ORM\Index(name="sensornames_ibfk_1", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorsRepository")
 */
class Sensors
{

    public const TEMPERATURE = 'Temperature';

    public const HUMIDITY = 'Humidity';

    public const ANALOG = 'Analog';

    public const LATITUDE   = 'Latitude';

    /**
     * @var int
     *
     * @ORM\Column(name="sensorNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorNameID;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorName", type="string", length=20, nullable=false)
     */
    private string $sensorName;

    /**
     * @var SensorType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\SensorType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorTypeID", referencedColumnName="sensorTypeID")
     * })
     */
    private SensorType $sensorTypeID;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Devices\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private Devices $deviceNameID;

    /**
     * @return int
     */
    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    /**
     * @param int $sensorNameID
     */
    public function setSensorNameID(int $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function getSensorTypeID(): SensorType
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(SensorType $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    /**
     * @return Devices
     */
    public function getDeviceNameID(): Devices
    {
        return $this->deviceNameID;
    }

    /**
     * @param Devices $deviceNameID
     */
    public function setDeviceNameID(Devices $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }
}
