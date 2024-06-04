<?php

namespace App\UserInterface\Services\NavBar;

use App\User\Entity\User;
use App\UserInterface\DTO\Response\NavBar\NavBarListLinkDTO;
use JetBrains\PhpStorm\ArrayShape;

interface NavBarDataProviderInterface
{
    #[ArrayShape([NavBarListLinkDTO::class])]
    public function getNavBarData(User $user): array;
}
