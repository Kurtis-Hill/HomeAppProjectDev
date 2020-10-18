<?php


namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="devicenames", indexes={@ORM\Index(name="Devices", columns={"deviceName"})})
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
     * @var devices
     *
     * @ORM\Column(name="deviceName", type="string", length=20, nullable=false)
     */
    private $devicename;

    /**
     * @var groupname
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceSecret", type="string", length=32, nullable=false)
     */
    private $secret;

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return Room
     */
    public function getRoomid()
    {
        return $this->roomid;
    }

    /**
     * @param int $roomid
     */
    public function setRoomid($roomid): void
    {
        $this->roomid = $roomid;
    }

    /**
     * @return groupname
     */
    public function getGroupnameid(): ?groupname
    {
        return $this->groupnameid;
    }

    /**
     * @param groupname $groupnameid
     */
    public function setGroupnameid(groupname $groupnameid): void
    {
        $this->groupnameid = $groupnameid;
    }

    /**
     * @return int
     */

    public function getDevicenameid()
    {
        return $this->devicenameid;
    }

    /**
     * @param int $devicenameid
     */
    public function setDevicenameid($devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }

    /**
     * @param string $devicename
     */
    public function setDevicename($devicename): void
    {
        $this->devicename = $devicename;
    }

    /**
     * @return string
     */
    public function getDevicename(): ?string
    {
        return $this->devicename;
    }

}