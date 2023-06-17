<?php

namespace App\User\Services\User;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\DTO\Internal\User\UserUpdateDTO;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\UserExceptions\CannotUpdateUsersGroupException;
use App\User\Exceptions\UserExceptions\IncorrectUserPasswordException;
use App\User\Exceptions\UserExceptions\NotAllowedToChangeUserRoleException;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use App\User\Voters\UserVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserHandler
{
    use ValidatorProcessorTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly Security $security,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface $validator,
        private readonly GroupRepositoryInterface $groupRepository,
    ) {}

    /**
     * @throws IncorrectUserPasswordException
     * @throws NotAllowedToChangeUserRoleException
     * @throws CannotUpdateUsersGroupException
     * @throws GroupNotFoundException
     */
    #[ArrayShape(['validationErrors'])]
    public function handleUserUpdate(UserUpdateDTO $userUpdateDTO): array
    {
        $userToUpdate = $userUpdateDTO->getUserToUpdate();
        if ($userUpdateDTO->getFirstName() !== null) {
            $userToUpdate->setFirstName($userUpdateDTO->getFirstName());
        }
        if ($userUpdateDTO->getLastName() !== null) {
            $userToUpdate->setLastName($userUpdateDTO->getLastName());
        }
        if ($userUpdateDTO->getEmail() !== null) {
            $userToUpdate->setEmail($userUpdateDTO->getEmail());
        }
        if ($userUpdateDTO->getRoles() !== null) {
            if (!$this->security->isGranted(UserVoter::CAN_UPDATE_USER_ROLES)) {
                throw new NotAllowedToChangeUserRoleException(NotAllowedToChangeUserRoleException::NOT_ALLOWED_TO_CHANGE_USER_ROLE);
            }
            $userToUpdate->setRoles($userUpdateDTO->getRoles());
        }
        if ($userUpdateDTO->getNewPassword() !== null) {
            $passwordVerified = $this->userPasswordHasher->isPasswordValid($userToUpdate, $userUpdateDTO->getOldPassword());
            if (!$passwordVerified) {
                throw new IncorrectUserPasswordException(IncorrectUserPasswordException::MESSAGE);
            }
            $userToUpdate->setPassword($userUpdateDTO->getNewPassword());
        }
        if ($userUpdateDTO->getGroupID() !== null) {
            if (!$this->security->isGranted(UserVoter::CAN_UPDATE_USER_GROUPS)) {
                throw new CannotUpdateUsersGroupException(CannotUpdateUsersGroupException::CANNOT_UPDATE_USERS_GROUP);
            }
            $groupName = $this->groupRepository->find($userUpdateDTO->getGroupID());
            if ($groupName === null) {
                throw new GroupNotFoundException(GroupNotFoundException::MESSAGE);
            }
            $userToUpdate->setGroup($groupName);
        }

        return $this->validateUserEntity($userToUpdate);
    }

    private function validateUserEntity(User $user): array
    {
        $validationErrors = $this->validator->validate($user);

        return $this->checkIfErrorsArePresent($validationErrors)
            ? $this->getValidationErrorAsArray($validationErrors)
            : [];
    }

    /**
      * @throws ORMException
      * @throws  OptimisticLockException
     */
    public function saveUser(User $user): void
    {
        $this->userRepository->persist($user);
        $this->userRepository->flush();
    }
}
