<?php


namespace App\Services;


use App\Services\AbstractHomeAppUserSensorServiceCore;
use http\Exception\RuntimeException;

class UserServiceUser extends AbstractHomeAppUserSensorServiceCore
{
    public function getAppUserDataForLocalStorage()
    {
        return [
            'userID' => $this->getUserID(),
            'roles' => $this->getUser()->getRoles(),
            'groups' => $this->getGroupNameDetails(),
        ];
    }
}
