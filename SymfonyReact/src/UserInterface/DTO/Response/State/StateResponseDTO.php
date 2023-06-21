<?php

namespace App\UserInterface\DTO\Response\State;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class StateResponseDTO
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

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardStateID(): int
    {
        return $this->cardStateID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardState(): string
    {
        return $this->cardState;
    }
}
