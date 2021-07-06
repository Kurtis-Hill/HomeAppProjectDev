<?php


namespace App\HomeAppSensorCore\Interfaces\Services;


use App\Entity\Core\User;

interface LoggedInUserRequiredInterface
{
    public function getUser(): ?User;
}
