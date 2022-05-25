<?php

namespace App\User\DTO\RequestDTOs;

use Symfony\Component\Validator\Constraints as Assert;

class AddNewRoomRequestDTO
{
    #[
        Assert\Type(type: 'string', message: 'roomName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "roomName cannot be null"
        ),
    ]
    private mixed $roomName = null;

    #[
        Assert\Type(type: 'integer', message: 'groupNameID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupNameID cannot be null"
        ),
    ]
    private mixed $groupNameID = null;

    public function getRoomName(): mixed
    {
        return $this->roomName;
    }

    public function setRoomName(mixed $roomName): void
    {
        $this->roomName = $roomName;
    }

    public function getGroupNameID(): mixed
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(mixed $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }
}
