<?php


namespace App\Services;


use App\HomeAppCore\HomeAppCoreAbstract;

class ProfilebarService extends HomeAppCoreAbstract
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
