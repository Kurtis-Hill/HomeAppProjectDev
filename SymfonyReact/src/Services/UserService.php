<?php


namespace App\Services;


use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use http\Exception\RuntimeException;

class UserService extends AbstractHomeAppSensorServiceCore
{
    public function getAppUserDataForLocalStorage()
    {
        return [
            'userID' => $this->getUserID(),
            'roles' => $this->getUserRoles()
        ];
    }
}
