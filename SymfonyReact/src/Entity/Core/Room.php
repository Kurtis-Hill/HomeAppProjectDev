<?php

namespace App\Entity\Core;

use App\Entity\Core\Groupname;
use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="room", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
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

    public function getRoomid(): ?int
    {
        return $this->roomid;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(string $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getGroupnameid(): ?Groupname
    {
        return $this->groupnameid;
    }

    public function setGroupnameid(?Groupname $groupnameid): self
    {
        $this->groupnameid = $groupnameid;

        return $this;
    }


}
