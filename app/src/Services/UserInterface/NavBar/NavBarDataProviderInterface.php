<?php

namespace App\Services\UserInterface\NavBar;

use App\DTOs\UserInterface\Response\NavBar\NavBarListLinkDTO;
use App\Entity\User\User;
use JetBrains\PhpStorm\ArrayShape;

interface NavBarDataProviderInterface
{
    #[ArrayShape([NavBarListLinkDTO::class])]
    public function getNavBarData(User $user): array;
}
