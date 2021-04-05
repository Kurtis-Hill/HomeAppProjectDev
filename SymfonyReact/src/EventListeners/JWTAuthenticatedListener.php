<?php


namespace App\EventListeners;


use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Services\UserServiceUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Security;

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

        if (is_callable($userCredentials, true)) {
            if ($user instanceof User) {
                try {
                    $groupNameMappingEntities = $this->em->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($user);
                    $user->setUserGroupMappingEntities($groupNameMappingEntities);
                } catch (\Exception $exception) {
                    error_log($exception->getMessage());
                }
            }
        }
    }
}
