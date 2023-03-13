<?php

namespace App\Authentication\EventListeners;

use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\User\Entity\User;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Psr\Log\LoggerInterface;

class JWTAuthenticatedListener
{
    private LoggerInterface $logger;

    public function __construct(GroupNameMappingRepository $groupNameMappingTableRepository, LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    /**
     * @param JWTAuthenticatedEvent $authenticatedEvent
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $authenticatedEvent): void
    {
        $authenticatedUser = $authenticatedEvent->getToken()->getUser();

        $userType = $authenticatedUser instanceof User
            ? 'User'
            : 'Device';

        $this->logger->info('User authenticated', [
            'user' => $authenticatedUser,
            'userType' => $userType,
            'time' => (new DateTimeImmutable('now'))->format('d/m/y H:i:s'),
            ]);
//        $user = $authenticatedEvent->getToken()->getUser();
//
//        $userCredentials = [$user, 'getUserID'];
//
//        if (is_callable($userCredentials, true) && $user instanceof UserExceptions) {
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
