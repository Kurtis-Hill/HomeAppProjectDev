<?php


namespace App\EventListeners;

use App\Entity\Core\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent)
    {
        $user = $authenticationSuccessEvent->getUser();

        if ($user instanceof User) {
            $data = $authenticationSuccessEvent->getData();
            $data['userData'] = [
                'userID' => $user->getUserID(),
                'roles' => $user->getRoles(),
            ];

            $authenticationSuccessEvent->setData($data);
        }
    }
}
