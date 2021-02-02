<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;
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
    private float $highTemp = 50;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lowTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="12"})
     */
    private float $lowTemp = 10;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private \DateTimeInterface $time;

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
    public function getSensorObject(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @return Devices
     */
    public function getDeviceObject(): Devices
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
     * @return int|float
     */
    public function getCurrentSensorReading(): int|float
    {
        return round($this->tempReading, 2);
    }

    /**
     * @return int|float
     */
    public function getHighReading(): int|float
    {
        return $this->highTemp;
    }

    /**
     * @return int|float
     */
    public function getLowReading(): int|float
    {
        return $this->lowTemp;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTimeInterface
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
    public function setTime(\DateTimeInterface $dateTime): void
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
     * @param bool $constrecord
     */
    public function setConstRecord(bool $constrecord): void
    {
        $this->constRecord = $constrecord;
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
