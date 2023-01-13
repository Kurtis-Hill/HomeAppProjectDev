<?php

namespace App\UserInterface\Services\NavBar;

use App\User\Entity\User;
use App\UserInterface\Builders\NavBarDTOBuilders\NavBarDTOBuilder;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;

interface NavBarDataProviderInterface
{
    public function getNavBarData(User $user): array;

    public function getNavbarRequestErrors(): array;
}
