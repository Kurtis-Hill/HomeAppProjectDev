<?php

namespace App\Authentication\EventListeners;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingTableRepository;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Psr\Log\LoggerInterface;

class JWTAuthenticatedListener
{
    private GroupNameMappingTableRepository $groupNameMappingTableRepository;

    private LoggerInterface $logger;

    public function __construct(GroupNameMappingTableRepository $groupNameMappingTableRepository, LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
        $this->groupNameMappingTableRepository = $groupNameMappingTableRepository;
    }

    /**
     * @param JWTAuthenticatedEvent $authenticatedEvent
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $authenticatedEvent): void
    {
        $user = $authenticatedEvent->getToken()->getUser();

        $userCredentials = [$user, 'getUserID'];

        if (is_callable($userCredentials, true) && $user instanceof User) {
            try {
                $groupNameMappingEntities = $this->groupNameMappingTableRepository->getAllGroupMappingEntitiesForUser($user);
                $user->setUserGroupMappingEntities($groupNameMappingEntities);
            } catch (ORMException $exception) {
                $authenticatedEvent->setPayload(['group name exception occurred']);
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
