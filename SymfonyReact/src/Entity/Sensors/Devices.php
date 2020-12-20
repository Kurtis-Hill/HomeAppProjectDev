<?php

namespace App\Entity\Sensors;

use App\Entity\Core\Room;
use Doctrine\ORM\Mapping as ORM;
use Proxies\__CG__\App\Entity\Core\Groupname;

/**
 * Devices
 *
 * @ORM\Table(name="devicenames", uniqueConstraints={@ORM\UniqueConstraint(name="deviceSecret", columns={"deviceSecret"})}, indexes={@ORM\Index(name="createdBy", columns={"createdBy"}), @ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="roomID", columns={"roomID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\DevicesRepository")
 */
class Devices
{
    /**
     * @var int
     *
     * @ORM\Column(name="deviceNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $devicenameid;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceName", type="string", length=20, nullable=false)
     */
    private $devicename;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceSecret", type="string", length=32, nullable=false)
     */
    private $devicesecret;

    /**
     * @var int
     *
     * @ORM\Column(name="createdBy", type="integer", nullable=false)
     */
    private $createdby;

    /**
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

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
     * @return int
     */
    public function getDevicenameid(): int
    {
        return $this->devicenameid;
    }

    /**
     * @param int $devicenameid
     */
    public function setDevicenameid(int $devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }

    /**
     * @return string
     */
    public function getDevicename(): string
    {
        return $this->devicename;
    }

    /**
     * @param string $devicename
     */
    public function setDevicename(string $devicename): void
    {
        $this->devicename = $devicename;
    }

    /**
     * @return string
     */
    public function getDevicesecret(): string
    {
        return $this->devicesecret;
    }

    /**
     * @param string $devicesecret
     */
    public function setDevicesecret(string $devicesecret): void
    {
        $this->devicesecret = $devicesecret;
    }

    /**
     * @return int
     */
    public function getCreatedby(): int
    {
        return $this->createdby;
    }

    /**
     * @param int $createdby
     */
    public function setCreatedby(int $createdby): void
    {
        $this->createdby = $createdby;
    }

    /**
     * @return Groupname
     */
    public function getGroupnameid(): Groupname
    {
        return $this->groupnameid;
    }

    /**
     * @param Groupname $groupnameid
     */
    public function setGroupnameid(Groupname $groupnameid): void
    {
        $this->groupnameid = $groupnameid;
    }

    /**
     * @return Room
     */
    public function getRoomid(): Room
    {
        return $this->roomid;
    }

    /**
     * @param Room $roomid
     */
    public function setRoomid(Room $roomid): void
    {
        $this->roomid = $roomid;
    }

}
