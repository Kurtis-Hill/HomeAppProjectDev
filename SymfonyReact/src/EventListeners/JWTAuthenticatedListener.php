<?php


namespace App\EventListeners;


use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;

class JWTAuthenticatedListener
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * JWTAuthenticatedListener constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
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
                $groupNameMappingEntities = $this->em->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($user);
                $user->setUserGroupMappingEntities($groupNameMappingEntities);
            } catch (ORMException $exception) {
                $authenticatedEvent->setPayload(['group name exception occurred']);
                error_log($exception->getMessage());
            }
        }
    }
}
