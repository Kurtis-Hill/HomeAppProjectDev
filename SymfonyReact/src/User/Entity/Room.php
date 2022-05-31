<?php

namespace App\User\Entity;

use App\Common\Form\CustomFormValidators\NoSpecialCharactersConstraint;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Room
 *
 * @ORM\Table(name="room", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\User\Repository\ORM\RoomRepository")
 */
class Room
{
    public const ALIAS = 'room';

    /**
     * @var int
     *
     * @ORM\Column(name="roomID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $roomID;

    #[
        NoSpecialCharactersConstraint,
        Assert\Length(
            min: 2,
            max: 20,
            minMessage: 'Room name must be at least {{ limit }} characters long',
            maxMessage: 'Room name cannot be longer than {{ limit }} characters',
        ),
        Assert\NotBlank,
    ]
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

    public function setGroupNameID(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }


}
