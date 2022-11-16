<?php

namespace App\User\Repository\ORM;

use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }


//    /**
//     * Used to upgrade (rehash) the user's password automatically over time.
//     * @throws ORMException|UnsupportedUserException
//     */
//    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
//    {
//    }

    public function showSSL()
    {
        $conn = $this->getEntityManager()->getConnection();

        $raw = "SHOW STATUS LIKE 'Ssl_cipher'";

        $stmt = $conn->prepare($raw);
        $result = $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findOneById(int $id): ?User
    {
        return $this->find($id);
    }

    public function persist(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
