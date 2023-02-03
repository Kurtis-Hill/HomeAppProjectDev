<?php

namespace App\User\Services\User;

use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\Logs\LogMessages;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupName\GroupNameBuilder;
use App\User\Builders\GroupNameMapping\GroupNameMappingBuilder;
use App\User\Builders\User\NewUserBuilder;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Exceptions\UserExceptions\UserCreationValidationErrorsException;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
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

    private GroupNameBuilder $groupNameBuilder;

    private GroupNameRepository $groupNameRepository;

    private NewUserBuilder $newUserBuilder;

    private UserRepository $userRepository;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    private string $profilePictureDir;

    private string $projectDir;

    public function __construct(
        GroupNameBuilder $groupNameBuilder,
        GroupNameRepository $groupNameRepository,
        NewUserBuilder $newUserBuilder,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        LoggerInterface $logger,
        string $profilePictureDir,
        string $projectDir,
    ) {
        $this->groupNameBuilder = $groupNameBuilder;
        $this->groupNameRepository = $groupNameRepository;
        $this->newUserBuilder = $newUserBuilder;
        $this->userRepository = $userRepository;
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
        $groupNameObject = $this->createUsersGroupName($groupName);

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
            throw new UserCreationValidationErrorsException($this->getValidationErrorAsArray($validationErrors));
        }

        $saveSuccess = $this->saveUser($user);

        if ($saveSuccess !== true) {
            throw new UserCreationValidationErrorsException(['Failed to save user']);
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
     * @throws GroupNameValidationException
     */
    private function createUsersGroupName(string $groupName): GroupNames
    {
        $groupNames = $this->groupNameBuilder->buildNewGroupName($groupName);

        $validationErrors = $this->validator->validate($groupNames);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupNameValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameRepository->persist($groupNames);
        $this->groupNameRepository->flush();

        return $groupNames;
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
