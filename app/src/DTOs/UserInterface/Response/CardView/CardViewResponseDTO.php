<?php

namespace App\DTOs\UserInterface\Response\CardView;

use App\DTOs\UserInterface\Response\Colours\ColourResponseDTO;
use App\DTOs\UserInterface\Response\Icons\IconResponseDTO;
use App\DTOs\UserInterface\Response\State\StateResponseDTO;
use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class CardViewResponseDTO
{
    private int $cardViewID;

    private IconResponseDTO $cardIcon;

    private ColourResponseDTO $cardColour;

    private StateResponseDTO $cardViewState;

    public function __construct(
        int $cardViewID,
        IconResponseDTO $cardIcon,
        ColourResponseDTO $cardColour,
        StateResponseDTO $cardStateDTO
    ) {
        $this->cardViewID = $cardViewID;
        $this->cardIcon = $cardIcon;
        $this->cardColour = $cardColour;
        $this->cardViewState = $cardStateDTO;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardIcon(): IconResponseDTO
    {
        return $this->cardIcon;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardColour(): ColourResponseDTO
    {
        return $this->cardColour;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCardViewState(): StateResponseDTO
    {
        return $this->cardViewState;
    }
}
