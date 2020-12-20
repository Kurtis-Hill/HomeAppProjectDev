<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Humidity
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"}), @ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\HumidRepository")
 */
class Humidity implements StandardSensorInterface
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
     * @var float
     *
     * @ORM\Column(name="humidReading", type="float", precision=10, scale=0, nullable=false)
     */
    private int|float $humidReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private int|float $highHumid;

    /**
     * @var float
     *
     * @ORM\Column(name="lowHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private int|float $lowHumid;

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
    private \DateTime $timez;

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private GroupNames $groupNameID;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private Room $roomID;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="Devices")
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
     * @return GroupNames
     */
    public function getGroupNameID(): GroupNames
    {
        return $this->groupNameID;
    }

    /**
     * @return Room
     */
    public function getRoomID(): Room
    {
        return $this->roomID;
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
     * @param GroupNames $groupNameID
     */
    public function setGroupNameID(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    /**
     * @param Room $roomID
     */
    public function setRoomID(Room $roomID): void
    {
        $this->roomID = $roomID;
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
    public function getCurrentSensorReading(): ?float
    {
        return $this->humidReading;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): ?float
    {
        return $this->highHumid;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): ?float
    {
        return $this->lowHumid;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->timez;
    }

    /**
     * @param float|null $reading
     */
    public function setCurrentSensorReading(?float $reading): void
    {
        $this->humidReading = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setHighReading(?float $reading): void
    {
        $this->highHumid = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setLowReading(?float $reading): void
    {
        $this->lowHumid = $reading;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setTime(\DateTime $dateTime): void
    {
        $this->timez = $dateTime;
    }

    /**
     * Sensor Functional Methods
     */

    /**
     * @return bool|null
     */
    public function getConstRecord(): ?bool
    {
        return $this->constRecord;
    }

    /**
     * @param bool|null $constRecord
     */
    public function setConstRecord(?bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }
}
