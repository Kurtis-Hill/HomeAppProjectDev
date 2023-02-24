<?php

namespace App\UserInterface\Services\NavBar;

use App\User\Entity\User;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarListLinkDTO;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;
use JetBrains\PhpStorm\ArrayShape;

interface NavBarDataProviderInterface
{
    #[ArrayShape([NavBarListLinkDTO::class])]
    public function getNavBarData(User $user): array;

    #[ArrayShape(['errors'])]
    public function getNavbarRequestErrors(): array;
}
