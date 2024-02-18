<?php

namespace App\UserInterface\DTO\Response\Colours;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class ColourResponseDTO
{
    private int $colourID;

    private string $colour;

    private string $shade;

    public function __construct(int $cardColourID, string $colour, string $shade)
    {
        $this->colourID = $cardColourID;
        $this->colour = $colour;
        $this->shade = $shade;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getColourID(): int
    {
        return $this->colourID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getColour(): string
    {
        return $this->colour;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getShade(): string
    {
        return $this->shade;
    }

}
