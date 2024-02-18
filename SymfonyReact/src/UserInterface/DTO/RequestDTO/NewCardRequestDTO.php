<?php

namespace App\UserInterface\DTO\RequestDTO;

use Symfony\Component\Validator\Constraints as Assert;

class NewCardRequestDTO
{
    #[Assert\Type(type: ['int', "null"], message: 'sensorID must be an {{ type }} you have provided {{ value }}')]
    private mixed $sensorID;

    #[Assert\Type(type: ['int', "null"], message: 'cardIcon must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardIcon = null;

    #[Assert\Type(type: ['int', "null"], message: 'cardColour must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardColour = null;

    #[Assert\Type(type: ['int', "null"], message: 'cardState must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardState = null;

    public function getSensorID(): mixed
    {
        return $this->sensorID;
    }

    public function setSensorID(mixed $sensorID): void
    {
        $this->sensorID = $sensorID;
    }

    public function getCardIcon(): mixed
    {
        return $this->cardIcon;
    }

    public function setCardIcon(mixed $cardIcon): void
    {
        $this->cardIcon = $cardIcon;
    }

    public function getCardColour(): mixed
    {
        return $this->cardColour;
    }

    public function setCardColour(mixed $cardColour): void
    {
        $this->cardColour = $cardColour;
    }

    public function getCardState(): mixed
    {
        return $this->cardState;
    }

    public function setCardState(mixed $cardState): void
    {
        $this->cardState = $cardState;
    }
}
