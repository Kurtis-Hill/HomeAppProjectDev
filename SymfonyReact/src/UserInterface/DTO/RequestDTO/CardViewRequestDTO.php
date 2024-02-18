<?php

namespace App\UserInterface\DTO\RequestDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CardViewRequestDTO
{
    #[Assert\Type(type: ['int', "null"], message: 'cardColour must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardColour = null;

    #[Assert\Type(type: ['int', "null"], message: 'cardIcon must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardIcon = null;

    #[Assert\Type(type: ['int', "null"], message: 'cardViewID must be an {{ type }} you have provided {{ value }}')]
    private mixed $cardViewState = null;

    public function getCardColour(): mixed
    {
        return $this->cardColour;
    }

    public function setCardColour(mixed $cardColour): void
    {
        $this->cardColour = $cardColour;
    }

    public function getCardIcon(): mixed
    {
        return $this->cardIcon;
    }

    public function setCardIcon(mixed $cardIcon): void
    {
        $this->cardIcon = $cardIcon;
    }

    public function getCardViewState(): mixed
    {
        return $this->cardViewState;
    }

    public function setCardViewState(mixed $cardViewState): void
    {
        $this->cardViewState = $cardViewState;
    }
}
