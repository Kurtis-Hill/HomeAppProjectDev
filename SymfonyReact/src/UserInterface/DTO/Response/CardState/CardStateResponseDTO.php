<?php

namespace App\UserInterface\DTO\Response\CardState;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardStateResponseDTO
{
    private int $cardStateID;

    private string $cardState;

    public function __construct(
        int $cardStateID,
        string $cardState,
    ) {
        $this->cardStateID = $cardStateID;
        $this->cardState = $cardState;
    }

    public function getCardStateID(): int
    {
        return $this->cardStateID;
    }

    public function getCardState(): string
    {
        return $this->cardState;
    }
}
