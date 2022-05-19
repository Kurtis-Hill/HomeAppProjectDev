<?php

namespace App\UserInterface\Services\NavBar;

use App\UserInterface\Exceptions\WrongUserTypeException;
use Symfony\Component\Security\Core\User\UserInterface;

interface NavBarServiceInterface
{
    /**
     * @throws WrongUserTypeException
     */
    public function getNavBarData(UserInterface $user): array;
}
