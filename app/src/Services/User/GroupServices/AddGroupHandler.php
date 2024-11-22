<?php

namespace App\Services\User\GroupServices;

use App\Builders\User\GroupName\GroupNameBuilder;
use App\Builders\User\GroupNameMapping\GroupNameMappingInternalDTOBuilder;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupMappingValidationException;
use App\Exceptions\User\GroupExceptions\GroupValidationException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Services\User\GroupMappingServices\AddGroupMappingHandler;
use App\Traits\ValidatorProcessorTrait;
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
     * @throws GroupValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NonUniqueResultException
     * @throws GroupMappingValidationException
     */
    public function addNewGroup(string $groupName, User $user = null): Group
    {
        $newGroupName = $this->groupNameBuilder->buildNewGroupName($groupName);

        $validationErrors = $this->validator->validate($newGroupName);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupValidationException($this->getValidationErrorAsArray($validationErrors));
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
