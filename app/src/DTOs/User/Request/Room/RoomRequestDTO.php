<?php

namespace App\DTOs\User\Request\Room;

use Symfony\Component\Validator\Constraints as Assert;

class RoomRequestDTO
{
    public function __construct(
        #[
            Assert\NotBlank,
            Assert\Type("string"),
            Assert\Length(min: 1, max: 255)
        ]
        private readonly string $roomName,
    ) {
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }
}
