<?php

namespace App\DTOs\UserInterface\Internal\CardDataFiltersDTO;

use App\Entity\Device\Devices;
use App\Entity\User\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardViewUriFilterDTO
{
    private ?Room $room;

    private ?Devices $device;

    public function __construct(
        ?Room $room = null,
        ?Devices $device = null,
    ) {
        $this->room = $room;
        $this->device = $device;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function getDevice(): ?Devices
    {
        return $this->device;
    }
}
