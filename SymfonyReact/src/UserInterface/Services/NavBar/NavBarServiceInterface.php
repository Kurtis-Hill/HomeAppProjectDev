<?php

namespace App\UserInterface\Services\NavBar;

use App\User\Entity\User;
use App\UserInterface\DTO\Response\NavBar\NavBarResponseDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;

interface NavBarServiceInterface
{
    /**
     * @throws WrongUserTypeException
     */
    public function getNavBarData(User $user): NavBarResponseDTO;
}
