<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Temp
 *
 * @ORM\Table(name="temp", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="temp_ibfk_6", columns={"deviceNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="GroupName", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\TempRepository")
 */
class Temperature implements StandardSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $tempID;

    /**
     * @var float
     *
     * @ORM\Column(name="tempReading", type="float", precision=10, scale=0, nullable=false)
     */
    private float $tempReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="26"})
     */
    private float $highTemp;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lowTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="12"})
     */
    private float $lowTemp;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private bool $constRecord;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private \DateTime $time;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private Devices $deviceNameID;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->tempID;
    }

    /**
     * @param int $tempID
     */
    public function setSensorID(int $tempID): void
    {
        $this->tempID = $tempID;
    }

    /**
     * @return Sensors
     */
    public function getSensorNameID(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @return Devices
     */
    public function getDeviceNameID(): Devices
    {
        return $this->deviceNameID;
    }

    /**
     * @param Sensors $id
     */
    public function setSensorNameID(Sensors $id): void
    {
        $this->sensorNameID = $id;
    }

    /**
     * @param Devices $deviceNameID
     */
    public function setDeviceNameID(Devices $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }


    /**
     * Sensor Reading Methods
     */

    /**
     * @return float|null
     */
    public function getCurrentSensorReading(): int|float
    {
        return $this->tempReading;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): int|float
    {
        return $this->highTemp;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): int|float
    {
        return $this->lowTemp;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @param int|float $reading
     */
    public function setCurrentSensorReading(int|float $reading): void
    {
        $this->tempReading = $reading;
    }

    /**
     * @param int|float $reading
     */
    public function setHighReading(int|float $reading): void
    {
        $this->highTemp = $reading;
    }

    /**
     * @param int|float $reading
     */
    public function setLowReading(int|float $reading): void
    {
        $this->lowTemp = $reading;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setTime(\DateTime $dateTime): void
    {
        $this->time = $dateTime;
    }

    /**
     * Sensor Functional Methods
     */

    /**
     * @return bool
     */
    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    /**
     * @param bool $constRecord
     */
    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    #[Pure] public function getMeasurementDifferenceHighReading(): int|float
    {
        return $this->getHighReading() - $this->getCurrentSensorReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentSensorReading();
    }
}
