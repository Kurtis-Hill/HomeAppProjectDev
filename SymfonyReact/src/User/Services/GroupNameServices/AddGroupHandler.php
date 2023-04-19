<?php

namespace App\User\Services\GroupNameServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Builders\GroupName\GroupNameBuilder;
use App\User\Builders\GroupNameMapping\GroupNameMappingBuilder;
use App\User\Builders\GroupNameMapping\GroupNameMappingInternalDTOBuilder;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameMappingValidationException;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Services\GroupMappingServices\AddGroupMappingHandler;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddGroupHandler
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private GroupNameBuilder $groupNameBuilder;

    private GroupRepositoryInterface $groupNameRepository;

    private AddGroupMappingHandler $addGroupNameMappingHandler;

    public function __construct(
        ValidatorInterface $validator,
        GroupNameBuilder $groupNameBuilder,
        GroupRepositoryInterface $groupNameRepository,
        AddGroupMappingHandler $addGroupNameMappingHandler,
    ) {
        $this->validator = $validator;
        $this->groupNameBuilder = $groupNameBuilder;
        $this->groupNameRepository = $groupNameRepository;
        $this->addGroupNameMappingHandler = $addGroupNameMappingHandler;
    }

    /**
     * @throws GroupNameValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NonUniqueResultException
     * @throws GroupNameMappingValidationException
     */
    public function addNewGroup(string $groupName, User $user = null): GroupNames
    {
        $newGroupName = $this->groupNameBuilder->buildNewGroupName($groupName);

        $validationErrors = $this->validator->validate($newGroupName);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupNameValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameRepository->persist($newGroupName);
        $this->groupNameRepository->flush();

        if ($user !== null && !$user->isAdmin()) {
            $addGroupNameMappingBuilder = GroupNameMappingInternalDTOBuilder::buildGroupNameMappingInternalDTO(
                $user,
                $newGroupName,
            );
            try {
                $this->addGroupNameMappingHandler->addNewGroupNameMappingEntry($addGroupNameMappingBuilder);
            } catch (ORMException|OptimisticLockException $e) {
                $this->groupNameRepository->remove($newGroupName);
                $this->groupNameRepository->flush();
                throw $e;
            }
        }

        return $newGroupName;
    }
}
