<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="room", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\RoomRepository")
 */
class Room
{
    /**
     * @var int
     *
     * @ORM\Column(name="roomID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $roomID;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=20, nullable=false)
     */
    private $room;

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameID;

    /**
     * @return int
     */
    public function getRoomID(): int
    {
        return $this->roomID;
    }

    /**
     * @param int $roomID
     */
    public function setRoomID(int $roomID): void
    {
        $this->roomID = $roomID;
    }

    /**
     * @return string
     */
    public function getRoom(): string
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    /**
     * @return GroupNames
     */
    public function getGroupnameID(): GroupNames
    {
        return $this->groupnameID;
    }

    /**
     * @param GroupNames $groupnameID
     */
    public function setGroupnameID(GroupNames $groupnameID): void
    {
        $this->groupnameID = $groupnameID;
    }


}
