<?php


namespace App\Services;


use App\HomeAppCore\HomeAppSensorServiceCoreAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class NavbarService extends HomeAppSensorServiceCoreAbstract
{
    private array $errors;

    public function getErrors()
    {
        return $this->getUserErrors();
    }
}
