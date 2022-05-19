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
        Assert\Type(type: 'integer', message: 'groupId must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupId cannot be null"
        ),
    ]
    private mixed $groupId = null;

    public function getRoomName(): mixed
    {
        return $this->roomName;
    }

    public function setRoomName(mixed $roomName): void
    {
        $this->roomName = $roomName;
    }

    public function getGroupId(): mixed
    {
        return $this->groupId;
    }

    public function setGroupId(mixed $groupId): void
    {
        $this->groupId = $groupId;
    }
}
