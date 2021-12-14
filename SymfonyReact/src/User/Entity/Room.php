<?php

namespace App\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\Column;

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
    private int $roomID;



    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Room name must be at least {{ limit }} characters long',
        maxMessage: 'Room name cannot be longer than {{ limit }} characters',
    )]
    /**
     * @Column(type="string")
     */
    #[Column(type: 'string', length: 20, nullable: false)]
    private string $room;

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private GroupNames $groupNameID;

    /**
     * @return int
     */
    public function getRoomID(): int
    {
        return $this->roomID;
    }

    #[NoReturn]
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

    #[NoReturn]
    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    /**
     * @return GroupNames
     */
    public function getGroupNameID(): GroupNames
    {
        return $this->groupNameID;
    }

    #[NoReturn]
    public function setGroupNameID(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }


}
