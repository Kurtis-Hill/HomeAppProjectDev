<?php


namespace App\Services;


use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class NavbarService extends AbstractHomeAppSensorServiceCore
{
    private array $errors;

    public function getNavBarData(): array
    {
        return  [
            'rooms' => $this->getUsersRooms(),
            'devices' => $this->getUsersDevices(),
            'groupNames' => $this->getGroupNameDetails()
        ];
    }

    public function getErrors()
    {
        return $this->getUserErrors();
    }
}
