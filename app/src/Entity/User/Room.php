<?php

namespace App\Entity\User;

use App\Repository\User\ORM\RoomRepository;
use App\Services\CustomValidators\NoSpecialCharactersNameConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: RoomRepository::class),
    ORM\Table(name: "room"),
    ORM\UniqueConstraint(name: "room", columns: ["room"]),
]
class Room
{
    public const ALIAS = 'room';

    #[
        ORM\Column(name: "roomID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $roomID;

    #[
        ORM\Column(name: "room", type: "string", length: 255, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: 2,
            max: 20,
            minMessage: 'Room name must be at least {{ limit }} characters long',
            maxMessage: 'Room name cannot be longer than {{ limit }} characters',
        ),
        Assert\NotBlank,
    ]
    private string $room;

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function setRoomID(int $roomID): void
    {
        $this->roomID = $roomID;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): void
    {
        $this->room = $room;
    }
}
