<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Humid
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"}), @ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\HumidRepository")
 */
class Humid implements StandardSensorInterface
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
     * @ORM\Column(name="humidReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $humidreading;

    /**
     * @var float
     *
     * @ORM\Column(name="highHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private $highhumid = '70';

    /**
     * @var float
     *
     * @ORM\Column(name="lowHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private $lowhumid = '15';

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private $constrecord;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $timez = 'current_timestamp()';

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var Devices
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
    public function getSensorID(): int
    {
        return $this->humidid;
    }

    public function setSensorID(int $id)
    {
        $this->humidid = $id;
    }


    /**
     * Sensor relational Objects
     */

    /**
     * @return GroupNames
     */
    public function getGroupNameID(): GroupNames
    {
        return $this->groupnameid;
    }

    /**
     * @return Room
     */
    public function getRoomID(): Room
    {
        return $this->roomid;
    }

    /**
     * @return Sensors
     */
    public function getSensorNameID(): Sensors
    {
        return $this->sensornameid;
    }

    /**
     * @return Devices
     */
    public function getDeviceNameID(): Devices
    {
        return $this->devicenameid;
    }


    /**
     * @param GroupNames $groupnameid
     */
    public function setGroupNameID(GroupNames $groupnameid): void
    {
        $this->groupnameid = $groupnameid;
    }

    /**
     * @param Room $roomid
     */
    public function setRoomID(Room $roomid): void
    {
        $this->roomid = $roomid;
    }

    /**
     * @param Sensors $id
     */
    public function setSensorNameID(Sensors $id): void
    {
        $this->sensornameid = $id;
    }

    /**
     * @param Devices $devicenameid
     */
    public function setDevicenameid(Devices $devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }


    /**
     * Sensor Reading Methods
     */

    /**
     * @return float|null
     */
    public function getCurrentSensorReading(): ?float
    {
        return $this->humidreading;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): ?float
    {
        return $this->highhumid;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): ?float
    {
        return $this->lowhumid;
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
        $this->humidreading = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setHighReading(?float $reading): void
    {
        $this->highhumid = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setLowReading(?float $reading): void
    {
        $this->lowhumid = $reading;
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
        return $this->constrecord;
    }

    /**
     * @param bool|null $constrecord
     */
    public function setConstRecord(?bool $constrecord): void
    {
        $this->constrecord = $constrecord;
    }
}
