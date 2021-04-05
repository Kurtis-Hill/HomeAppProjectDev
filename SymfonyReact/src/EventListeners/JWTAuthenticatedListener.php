<?php


namespace App\EventListeners;


use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Services\UserServiceUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Security;

class JWTAuthenticatedListener
{
    private $em;
    private $userService;
    private $security;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    public function onJWTAuthenticated(JWTAuthenticatedEvent $authenticatedEvent, $a, TraceableEventDispatcher $b)
    {
        //        dd($authenticationSuccessEvent, $security, $entityManager);
        /** @var User $user */
//        $user = $authenticatedEvent->getUser();
//       $authenticatedEvent->
        $user = $authenticatedEvent->getToken()->getUser();
//        $authenticationSuccessEvent->

        $groupNameIds = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($authenticatedEvent->getToken()->getUser());
//        $this->userService->setUserGroups($groupNameIds);
//        dd($authenticatedEvent->getToken()->getUser()->setUserGroups($groupNameIds), $this->security, $this->userService, $groupNameIds, $this->userService->getUser(), $authenticatedEvent->getPayload(), $a, $b);
        $user->setGroupTest($groupNameIds);
//        dd($groupNameIds);
//        dd('its there!!');
//       $data = $authenticationSuccessEvent
//        echo "success";die;
    }
}
