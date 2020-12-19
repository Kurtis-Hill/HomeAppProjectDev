<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"}), @ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"})}, indexes={@ORM\Index(name="roomID", columns={"roomID"}), @ORM\Index(name="groupNameID", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\LatitudeRepository")
 */
class Latitude implements StandardSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="latitudeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $latitudeid;

    /**
     * @var int
     *
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    private $latitude;

    /**
     * @var int
     *
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    private $highLatitude;

    /**
     * @var int
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private $lowLatitude;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private $constrecord;

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
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

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
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $timez = 'current_timestamp()';

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->latitudeid;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->latitudeid = $analogid;
    }

    /**
     * Sensor relational Objects
     */

    /**
     * @return Groupname
     */
    public function getGroupNameID(): Groupname
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
     * @param Groupname $groupnameid
     */
    public function setGroupNameID(Groupname $groupnameid): void
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
        return $this->latitude;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): ?float
    {
        return $this->highLatitude;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): ?float
    {
        return $this->lowLatitude;
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
        $this->latitude = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setHighReading(?float $reading): void
    {
        $this->highLatitude = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setLowReading(?float $reading): void
    {
        $this->lowLatitude = $reading;
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
    public function getConstrecord(): ?bool
    {
        return $this->constrecord;
    }

    /**
     * @param bool|null $constrecord
     */
    public function setConstrecord(?bool $constrecord): void
    {
        $this->constrecord = $constrecord;
    }
}
