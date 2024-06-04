<?php

namespace App\User\Builders\User;

use App\User\Entity\Group;
use App\User\Entity\User;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NewUserBuilder
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildNewUser(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        array $roles,
        Group $groupNameObject,
        ?string $profilePic,
    ): User {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setGroup($groupNameObject);
        $user->setCreatedAt(new DateTimeImmutable('now'));

        if ($profilePic !== null) {
            $user->setProfilePic($profilePic);
        }

        $user->setPassword(
            $password
        );
        return $user;
    }

    public function hashUserPassword(User $user, string $password): User
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $password
            )
        );

        return $user;
    }
}
