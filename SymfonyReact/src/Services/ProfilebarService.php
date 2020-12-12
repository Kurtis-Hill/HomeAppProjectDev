<?php


namespace App\Services;


use App\HomeAppCore\HomeAppCoreAbstract;

class ProfilebarService extends HomeAppCoreAbstract
{
    public function getProfilePic()
    {
        return $this->user->getUser()->getProfilepic();
    }

    public function getGroupName()
    {
        return $this->user->getUser()->getGroupNameId()->getGroupName();
    }

    public function getFullUserName()
    {
        return sprintf("%s %s",
            ucfirst($this->user->getUser()->getFirstname()),
            ucfirst($this->user->getUser()->getLastname())
            );
    }

}
