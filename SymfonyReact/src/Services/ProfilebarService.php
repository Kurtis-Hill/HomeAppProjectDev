<?php


namespace App\Services;


use App\HomeAppCore\HomeAppRoomAbstract;

class ProfilebarService extends HomeAppRoomAbstract
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