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
    private $roomid;

    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=20, nullable=false)
     */
    private $room;

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
     * @return int
     */
    public function getRoomid(): int
    {
        return $this->roomid;
    }

    /**
     * @param int $roomid
     */
    public function setRoomid(int $roomid): void
    {
        $this->roomid = $roomid;
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


}
