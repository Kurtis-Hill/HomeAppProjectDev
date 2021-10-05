<?php

namespace App\EventListeners;

use App\Entity\Core\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessListener
{
    private $requestStack;
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
//        $this->request = $request;
    }
    /**
     * @param AuthenticationSuccessEvent $authenticationSuccessEvent
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        dd($this->requestStack->getCurrentRequest()->);
//        dd($this->request->get('ipAddress'));
        $user = $authenticationSuccessEvent->getUser();

//        dd();
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
