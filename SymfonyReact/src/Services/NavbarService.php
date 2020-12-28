<?php


namespace App\Services;


use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class NavbarService extends AbstractHomeAppSensorServiceCore
{
    private array $errors;

    public function getErrors()
    {
        return $this->getUserErrors();
    }
}
