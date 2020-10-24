<?php


namespace App\Listeners;


use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationListener(AuthenticationSuccessEvent $authenticationSuccessEvent)
    {
    //    dd('fuyck');
//       $data = $authenticationSuccessEvent
//        echo "success";die;
    }
}