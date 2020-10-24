<?php


namespace App\Services;


use App\HomeAppCore\HomeAppRoomAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService extends HomeAppRoomAbstract
{
    public function getErrors()
    {
        $userErrors = $this->getUserErrors();

        return $userErrors;
    }
}