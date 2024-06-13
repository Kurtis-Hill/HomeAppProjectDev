<?php

namespace App\DTOs\User\Request;

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


    public function getRoomName(): mixed
    {
        return $this->roomName;
    }

    public function setRoomName(mixed $roomName): void
    {
        $this->roomName = $roomName;
    }
}
