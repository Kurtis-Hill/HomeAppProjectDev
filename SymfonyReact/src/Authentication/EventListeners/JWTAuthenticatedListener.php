<?php

namespace App\Authentication\EventListeners;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Psr\Log\LoggerInterface;

class JWTAuthenticatedListener
{
    private GroupNameMappingRepository $groupNameMappingTableRepository;

    private LoggerInterface $logger;

    public function __construct(GroupNameMappingRepository $groupNameMappingTableRepository, LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
        $this->groupNameMappingTableRepository = $groupNameMappingTableRepository;
    }

    /**
     * @param JWTAuthenticatedEvent $authenticatedEvent
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $authenticatedEvent): void
    {
        //@TODO remove i think
//        $user = $authenticatedEvent->getToken()->getUser();
//
//        $userCredentials = [$user, 'getUserID'];
//
//        if (is_callable($userCredentials, true) && $user instanceof User) {
//            try {
//                $groupNameMappingEntities = $this->groupNameMappingTableRepository->getAllGroupMappingEntitiesForUser($user);
//                $user->setUserGroupMappingEntities($groupNameMappingEntities);
//            } catch (ORMException $exception) {
//                $authenticatedEvent->setPayload(['group name exception occurred']);
//                $this->logger->error($exception->getMessage());
//            }
//        }
    }
}
