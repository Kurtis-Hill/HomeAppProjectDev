<?php

namespace App\UserInterface\Services\Cards\UsersCardSelectionService;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

interface UsersCardSelectionServiceInterface
{
    #[ArrayShape(
        [
            'iconID' => "int",
            'iconName' => "string",
            'description' => "string"
        ]
    )]
    public function getUsersStandardCardSelectionsAsArray(): array;

    #[ArrayShape(
        [
            'icons' => [IconResponseDTO::class],
            'colours' => [ColourResponseDTO::class],
            'states' => [CardStateResponseDTO::class]
        ]
    )]
    public function getUsersCardSelectionAsDTOs(): array;
}
