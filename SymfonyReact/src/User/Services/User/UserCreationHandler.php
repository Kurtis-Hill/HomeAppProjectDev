<?php

namespace App\User\Services\User;

use App\Common\Logs\LogMessages;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupNameMapping\GroupNameMappingInternalDTOBuilder;
use App\User\Builders\User\NewUserBuilder;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\GroupExceptions\GroupValidationException;
use App\User\Exceptions\UserExceptions\UserCreationValidationErrorsException;
use App\User\Repository\ORM\GroupRepository;
use App\User\Repository\ORM\UserRepository;
use App\User\Services\GroupMappingServices\AddGroupMappingHandler;
use App\User\Services\GroupServices\AddGroupHandler;
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

    private GroupRepository $groupRepository;

    private NewUserBuilder $newUserBuilder;

    private UserRepository $userRepository;

    private AddGroupHandler $addGroupHandler;

    private AddGroupMappingHandler $addGroupMappingHandler;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    private string $profilePictureDir;

    private string $projectDir;

    public function __construct(
        GroupRepository $groupNameRepository,
        NewUserBuilder $newUserBuilder,
        UserRepository $userRepository,
        AddGroupHandler $addGroupNameHandler,
        AddGroupMappingHandler $addGroupNameMappingHandler,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        string $profilePictureDir,
        string $projectDir,
    ) {
        $this->groupRepository = $groupNameRepository;
        $this->newUserBuilder = $newUserBuilder;
        $this->userRepository = $userRepository;
        $this->addGroupHandler = $addGroupNameHandler;
        $this->addGroupMappingHandler = $addGroupNameMappingHandler;
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
     * @throws GroupValidationException
     * @throws GroupNotFoundException
     */
    public function handleNewUserCreation(
        string $firstName,
        string $lastName,
        string $email,
        string $groupName,
        string $password,
        ?UploadedFile $profilePic = null,
        array $roles = ['ROLE_USER'],
        bool $userApproved = false,
    ): User {
        $groupNameObject = $this->addGroupHandler->addNewGroup($groupName, null);

        if ($profilePic instanceof UploadedFile) {
            $profilePicFileName = $this->handleProfilePicFileUpload($profilePic);
        }

        $user = $this->newUserBuilder->buildNewUser(
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
            $this->groupRepository->remove($groupNameObject);
            throw new UserCreationValidationErrorsException($this->getValidationErrorAsArray($validationErrors));
        }

        $saveSuccess = $this->saveUser($user);

        if ($saveSuccess !== true) {
            $this->groupRepository->remove($groupNameObject);
            throw new UserCreationValidationErrorsException(['Failed to save user']);
        }

        if ($userApproved === true && !$user->isAdmin()) {
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
     * @throws GroupNotFoundException
     */
    private function addNewUserToSharedUserGroups(User $user): void
    {
        $sharedUserGroup = $this->groupRepository->findOneBy(['groupName' => Group::HOME_APP_GROUP_NAME]);
        if (!$sharedUserGroup instanceof Group) {
            $this->logger->error(
                sprintf(
                    'Failed to create group mapping entry for shared user group for userID %d at %s',
                    $user->getUserID(),
                    (new DateTimeImmutable('now'))->format('d-M-Y H:i:s'),
                )
            );
            throw new GroupNotFoundException('Base group name not found, contact your system admins');
        }

        $newGroupNameMappingDTO = GroupNameMappingInternalDTOBuilder::buildGroupNameMappingInternalDTO(
            $user,
            $sharedUserGroup,
        );

        $this->addGroupMappingHandler->addNewGroupNameMappingEntry(
            $newGroupNameMappingDTO
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
            $this->groupRepository->remove($user->getGroup());
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
