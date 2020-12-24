<?php


namespace App\Services;


use App\HomeAppSensorCore\HomeAppSensorServiceCoreAbstract;

class ProfilebarService extends HomeAppSensorServiceCoreAbstract
{
    public function getProfilePic()
    {
        return $this->user->getUser()->getProfilePic();
    }

    public function getGroupName()
    {
        return $this->user->getUser()->getGroupNameID()->getGroupName();
    }

    public function getFullUserName()
    {
        return sprintf("%s %s",
            ucfirst($this->user->getUser()->getFirstName()),
            ucfirst($this->user->getUser()->getLastName())
            );
    }

}
