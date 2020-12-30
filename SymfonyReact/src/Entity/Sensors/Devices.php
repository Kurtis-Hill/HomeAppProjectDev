<?php

namespace App\Entity\Sensors;


use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use Doctrine\ORM\Mapping as ORM;


/**
 * Devices
 *
 * @ORM\Table(name="devicenames", uniqueConstraints={@ORM\UniqueConstraint(name="deviceSecret", columns={"deviceSecret"})}, indexes={@ORM\Index(name="createdBy", columns={"createdBy"})})
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
    private int $deviceNameID;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceName", type="string", length=20, nullable=false)
     */
    private string $deviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceSecret", type="string", length=32, nullable=false)
     */
    private string $deviceSecret;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createdBy", referencedColumnName="userID")
     * })
     */
    private User $createdBy;

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
     * @return int
     */
    public function getDeviceNameID(): int
    {
        return $this->deviceNameID;
    }

    /**
     * @param int $deviceNameID
     */
    public function setDeviceNameID(int $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }

    /**
     * @return string
     */
    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    /**
     * @param string $deviceName
     */
    public function setDeviceName(string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    /**
     * @return string
     */
    public function getDeviceSecret(): string
    {
        return $this->deviceSecret;
    }

    /**
     * @param string $deviceSecret
     */
    public function setDeviceSecret(string $deviceSecret): void
    {
        $this->deviceSecret = $deviceSecret;
    }

    /**
     * @return int
     */
    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return GroupNames
     */
    public function getGroupNameObject(): GroupNames
    {
        return $this->groupNameID;
    }

    /**
     * @param GroupNames $groupNameID
     */
    public function setGroupNameObject(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    /**
     * @return Room
     */
    public function getRoomObject(): Room
    {
        return $this->roomID;
    }

    /**
     * @param Room $roomID
     */
    public function setRoomObject(Room $roomID): void
    {
        $this->roomID = $roomID;
    }

}
