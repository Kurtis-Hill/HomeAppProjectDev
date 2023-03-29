<?php

namespace App\User\Services\User;

use App\Common\Logs\LogMessages;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\User\NewUserBuilder;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Exceptions\UserExceptions\UserCreationValidationErrorsException;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use App\User\Services\GroupMappingServices\AddGroupNameMappingHandler;
use App\User\Services\GroupNameServices\AddGroupNameHandler;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCreationHandler
{
    use ValidatorProcessorTrait;

    private GroupNameRepository $groupNameRepository;

    private NewUserBuilder $newUserBuilder;

    private UserRepository $userRepository;

    private AddGroupNameHandler $addGroupNameHandler;

    private AddGroupNameMappingHandler $addGroupNameMappingHandler;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    private string $profilePictureDir;

    private string $projectDir;

    public function __construct(
        GroupNameRepository $groupNameRepository,
        NewUserBuilder $newUserBuilder,
        UserRepository $userRepository,
        AddGroupNameHandler $addGroupNameHandler,
        AddGroupNameMappingHandler $addGroupNameMappingHandler,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        string $profilePictureDir,
        string $projectDir,
    ) {
        $this->groupNameRepository = $groupNameRepository;
        $this->newUserBuilder = $newUserBuilder;
        $this->userRepository = $userRepository;
        $this->addGroupNameHandler = $addGroupNameHandler;
        $this->addGroupNameMappingHandler = $addGroupNameMappingHandler;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->profilePictureDir = $profilePictureDir;
        $this->projectDir = $projectDir;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UniqueConstraintViolationException
     * @throws UserCreationValidationErrorsException
     * @throws GroupNameValidationException
     * @throws GroupNameNotFoundException
     */
    public function handleNewUserCreation(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $groupName,
        ?UploadedFile $profilePic = null,
        array $roles = ['ROLE_USER'],
    ): User {
        $groupNameObject = $this->addGroupNameHandler->addNewGroup($groupName, null);

        if ($profilePic instanceof UploadedFile) {
            $profilePicFileName = $this->handleProfilePicFileUpload($profilePic);
        }

        $user =  $this->newUserBuilder->buildNewUser(
            $firstName,
            $lastName,
            $email,
            $password,
            $roles,
            $groupNameObject,
            $profilePicFileName ?? null,
        );

        $validationErrors = $this->validator->validate($user);

        $this->newUserBuilder->hashUserPassword($user, $password);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            $this->groupNameRepository->remove($groupNameObject);
            throw new UserCreationValidationErrorsException($this->getValidationErrorAsArray($validationErrors));
        }

        $saveSuccess = $this->saveUser($user);

        if ($saveSuccess !== true) {
            $this->groupNameRepository->remove($groupNameObject);
            throw new UserCreationValidationErrorsException(['Failed to save user']);
        }

        if (!$user->isAdmin()) {
            $this->addNewUserToSharedUserGroups($user);
        }

        $this->logger->info(
            sprintf(
                LogMessages::NEW_USER_CREATED,
                $user->getUserID(),
                $user->getCreatedAt()?->format('d-M-Y H:i:s')
            )
        );

        return $user;
    }

    /**
     * @throws GroupNameNotFoundException
     */
    private function addNewUserToSharedUserGroups(User $user): void
    {
        $sharedUserGroup = $this->groupNameRepository->findOneBy(['groupName' => GroupNames::HOME_APP_GROUP_NAME]);
        if (!$sharedUserGroup instanceof GroupNames) {
            $this->logger->error(
                sprintf(
                    'Failed to create group mapping entry for shared user group for userID %d at %s',
                    $user->getUserID(),
                    (new DateTimeImmutable('now'))->format('d-M-Y H:i:s'),
                )
            );
            throw new GroupNameNotFoundException('Base group name not found, contact your system admins');
        }

        $this->addGroupNameMappingHandler->addNewGroupNameMappingEntry(
            $sharedUserGroup,
            $user,
        );
    }

    //@TODO - move this to a service
    private function handleProfilePicFileUpload(UploadedFile $profilePic): ?string
    {
        try {
            $newName = uniqid('profilePic_', true) . $profilePic->getClientOriginalName();
            $profilePic->move(
                $this->projectDir . $this->profilePictureDir,
                $newName
            );
        } catch (FileException) {
            $this->logger->error(
                sprintf(
                    LogMessages::ERROR_UPLOADING_PROFILE_PIC,
                    (new DateTimeImmutable('now'))->format('d-M-Y H:i:s'),
                )
            );

            return null;
        }

        return $newName ?? null;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveUser(User $user): bool
    {
        try {
            $this->userRepository->persist($user);
            $this->userRepository->flush();

            return true;
        } catch (ORMException|OptimisticLockException) {
            $this->groupNameRepository->remove($user->getGroupNameID());
            $this->logger->error(
                sprintf(
                    LogMessages::ERROR_CREATING_NEW_USER,
                    (new DateTimeImmutable('now'))->format('d-M-Y H:i:s'),
                )
            );
        }

        return false;
    }
}
