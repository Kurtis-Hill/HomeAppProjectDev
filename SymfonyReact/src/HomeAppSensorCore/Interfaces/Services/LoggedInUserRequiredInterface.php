<?php


namespace App\HomeAppSensorCore\Interfaces\Services;


use App\User\Entity\User;

interface LoggedInUserRequiredInterface
{
    public function getUser(): ?User;
}
