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
        $userName = ucfirst($this->user->getUser()->getFirstname());
        $userName .= " ";
        $userName .= ucfirst($this->user->getUser()->getLastname());

        return $userName;
    }

}