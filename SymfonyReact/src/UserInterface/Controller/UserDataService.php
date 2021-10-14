<?php

namespace App\UserInterface\Controller;

use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use Symfony\Component\Security\Core\Security;

class UserDataService implements APIErrorInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getServerErrors(): array
    {
        // TODO: Implement getServerErrors() method.
    }

    public function getUserInputErrors(): array
    {
        // TODO: Implement getUserInputErrors() method.
    }
}
