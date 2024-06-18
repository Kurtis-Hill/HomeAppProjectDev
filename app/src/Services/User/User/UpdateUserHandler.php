<?php

namespace App\Services\User\User;

use App\DTOs\User\Internal\User\UserUpdateDTO;
use App\Entity\User\User;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\UserExceptions\CannotUpdateUsersGroupException;
use App\Exceptions\User\UserExceptions\IncorrectUserPasswordException;
use App\Exceptions\User\UserExceptions\NotAllowedToChangeUserRoleException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\UserVoter;
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
     * @throws \App\Exceptions\User\GroupExceptions\GroupNotFoundException
     * @throws NotAllowedToUpdatePasswordException
     * @throws UserNotAllowedException
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
            if (!$this->security->isGranted(UserVoter::CAN_UPDATE_USER_PASSWORD, $userToUpdate)) {
                throw new NotAllowedToUpdatePasswordException(NotAllowedToUpdatePasswordException::NOT_ALLOWED_TO_UPDATE_PASSWORD);
            }
            $user = $this->security->getUser();
            if (!$user instanceof User) {
                throw new UserNotAllowedException();
            }
            if (!$user->isAdmin()) {
                $passwordVerified = $this->userPasswordHasher->isPasswordValid($userToUpdate, $userUpdateDTO->getOldPassword() ?? '');
                if (!$passwordVerified) {
                    throw new IncorrectUserPasswordException(IncorrectUserPasswordException::MESSAGE);
                }
            }
            $hashedPassword = $this->userPasswordHasher->hashPassword(
                $userToUpdate,
                $userUpdateDTO->getNewPassword()
            );

            $userToUpdate->setPassword($hashedPassword);
        }
        if ($userUpdateDTO->getGroupID() !== null) {
            if (!$this->security->isGranted(UserVoter::CAN_UPDATE_USER_GROUPS)) {
                throw new CannotUpdateUsersGroupException(CannotUpdateUsersGroupException::CANNOT_UPDATE_USERS_GROUP);
            }
            $groupName = $this->groupRepository->find($userUpdateDTO->getGroupID());
            if ($groupName === null) {
                throw new GroupNotFoundException(sprintf(GroupNotFoundException::MESSAGE, $userUpdateDTO->getGroupID()));
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
