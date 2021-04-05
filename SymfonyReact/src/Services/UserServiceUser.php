<?php


namespace App\Services;


use App\Entity\Core\User;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Namshi\JOSE\JWT;
use Symfony\Component\Security\Core\Security;

class UserServiceUser
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
//        dd($security, $JWTUser, 'user service construct');
    }

    public function setUserGroups(array $groupMappings)
    {

        if ($this->user instanceof User) {
            $this->user->setGroupTest($groupMappings);
//            dd($groupMappings, 'mappings user service', $this->user);
        }
    }

    public function getUser()
    {
        return $this->user;
    }

//    public function getAppUserDataForLocalStorage()
//    {
//        return [
//            'userID' => $this->getUserID(),
//            'roles' => $this->getUser()->getRoles(),
//            'groups' => $this->getGroupNameDetails(),
//        ];
//    }
}
