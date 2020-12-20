<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Analog
 *
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="analog_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="roomID", columns={"roomID"}), @ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="analog_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\AnalogRepository")
 */
class Analog implements StandardSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $analogid;

    /**
     * @var float|null
     *
     * @ORM\Column(name="analogReading", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $analogreading = 'NULL';

    /**
     * @var float|null
     *
     * @ORM\Column(name="highAnalog", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $highanalog = 'NULL';

    /**
     * @var float|null
     *
     * @ORM\Column(name="lowAnalog", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $lowanalog = 'NULL';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true, options={"default"="1"})
     */
    private $constrecord = true;

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
        return $this->analogid;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->analogid = $analogid;
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
     * @param Sensors $sensornameid
     */
    public function setSensorNameID(Sensors $sensornameid): void
    {
        $this->sensornameid = $sensornameid;
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
        return $this->analogreading;
    }

    /**
     * @return float|null
     */
    public function getHighReading(): ?float
    {
        return $this->highanalog;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): ?float
    {
        return $this->lowanalog;
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
        $this->analogreading = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setHighReading(?float $reading): void
    {
        $this->highanalog = $reading;
    }

    /**
     * @param float|null $reading
     */
    public function setLowReading(?float $reading): void
    {
        $this->lowanalog = $reading;
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
