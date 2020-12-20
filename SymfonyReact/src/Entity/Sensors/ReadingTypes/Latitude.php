<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
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
    private int|float $latitudeID;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    private int|float $latitude;

    /**
     * @var int
     *
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    private int|float $highLatitude;

    /**
     * @var int
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private int|float $lowLatitude;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private bool $constRecord;

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
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private GroupNames $groupNameID;

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
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private \DateTime $timez;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->latitudeID = $analogid;
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
    public function getCurrentSensorReading(): float
    {
        return $this->latitude;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): float
    {
        return $this->highLatitude;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): float
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
