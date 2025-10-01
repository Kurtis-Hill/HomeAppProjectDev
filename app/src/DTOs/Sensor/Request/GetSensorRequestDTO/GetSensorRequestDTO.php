<?php

namespace App\DTOs\Sensor\Request\GetSensorRequestDTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetSensorRequestDTO
{
    #[Assert\Type(type: ['array', "null"], message: 'deviceIDs must be a {{ type }} you have provided {{ value }}'), ]
    private ?array $deviceIDs;

    #[Assert\Type(type: ['array', "null"], message: 'deviceNames must be a {{ type }} you have provided {{ value }}'), ]
    private ?array $deviceNames;

    #[Assert\Type(type: ['array', "null"], message: 'groupIDs must be a {{ type }} you have provided {{ value }}'), ]
    private ?array $groupIDs;

    #[Assert\Type(type: ['array', "null"], message: 'responseType must be a {{ type }} you have provided {{ value }}'), ]
    private ?array $cardViewIDs;

    public function getDeviceIDs(): ?array
    {
        return $this->deviceIDs;
    }

    public function setDeviceIDs(?array $deviceIDs): void
    {
        $this->deviceIDs = $deviceIDs;
    }

    public function getDeviceNames(): ?array
    {
        return $this->deviceNames;
    }

    public function setDeviceNames(?array $deviceNames): void
    {
        $this->deviceNames = $deviceNames;
    }

    public function getGroupIDs(): ?array
    {
        return $this->groupIDs;
    }

    public function setGroupIDs(?array $groupIDs): void
    {
        $this->groupIDs = $groupIDs;
    }

    public function getCardViewIDs(): ?array
    {
        return $this->cardViewIDs;
    }

    public function setCardViewIDs(?array $cardViewIDs): void
    {
        $this->cardViewIDs = $cardViewIDs;
    }
}
