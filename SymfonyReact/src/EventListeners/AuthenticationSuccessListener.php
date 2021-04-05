<?php


namespace App\EventListeners;


use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Services\UserServiceUser;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Security;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    private $em;
    private $userService;

    public function __construct(EntityManagerInterface $entityManager, UserServiceUser $userServiceUser)
    {
        $this->userService = $userServiceUser;
        $this->em = $entityManager;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent, $security, $entityManager)
    {
//        dd($authenticationSuccessEvent, $security, $entityManager);
        /** @var User $user */
        $user = $authenticationSuccessEvent->getUser();
//        $authenticationSuccessEvent->

        $groupNameIds = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($user);
        $this->userService->setUserGroups($groupNameIds);
//        dd($groupNameIds);
//        dd('its there!!');
//       $data = $authenticationSuccessEvent
//        echo "success";die;
    }
}
