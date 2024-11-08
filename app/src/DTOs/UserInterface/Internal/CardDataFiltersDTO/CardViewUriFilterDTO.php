<?php

namespace App\DTOs\UserInterface\Internal\CardDataFiltersDTO;

use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardViewUriFilterDTO
{
    public function __construct(
        private ?Room $room = null,
        private ?Devices $device = null,
        private ?Group $group = null,
    ) {
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function getDevice(): ?Devices
    {
        return $this->device;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }
}
