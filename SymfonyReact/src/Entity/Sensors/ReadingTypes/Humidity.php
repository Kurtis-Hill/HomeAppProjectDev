<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Humidity
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"}), @ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\HumidRepository")
 */
class Humidity implements StandardReadingSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $humidID;

    /**
     * @var int
     *
     * @ORM\Column(name="humidReading", type="integer", precision=10, scale=0, nullable=false)
     */
    private int $humidReading;

    /**
     * @var int
     *
     * @ORM\Column(name="highHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private int $highHumid = 80;

    /**
     * @var int
     *
     * @ORM\Column(name="lowHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private int $lowHumid = 10;

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
    private ?\DateTime $time;

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
    public function getSensorID(): int
    {
        return $this->humidID;
    }

    public function setSensorID(int $id)
    {
        $this->humidID = $id;
    }


    /**
     * Sensor relational Objects
     */

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

    public function getCurrentReading(): int|float
    {
        return $this->humidReading;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): int|float
    {
        return $this->highHumid;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): int|float
    {
        return $this->lowHumid;
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
        $this->humidReading = $reading;
    }

    /**
     * @param int|float $reading
     */
    public function setHighReading(int|float $reading): void
    {
        $this->highHumid = $reading;
    }

    /**
     * @param int|float $reading
     */
    public function setLowReading(int|float $reading): void
    {
        $this->lowHumid = $reading;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setTime(?\DateTime $time = null): void
    {
        if ($time === null) {
            $time = new \DateTime('now');
        }

        $this->time = $time;
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
        return $this->getHighReading() - $this->getCurrentReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentReading();
    }

    public function isReadingOutOfBounds(): bool
    {
        if ($this->getCurrentReading() <= $this->getHighReading()) {
            return true;
        }
        if ($this->getCurrentReading() <= $this->getLowReading()) {
            return true;
        }

        return false;
    }
}
