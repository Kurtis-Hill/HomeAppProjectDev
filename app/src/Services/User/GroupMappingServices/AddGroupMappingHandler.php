<?php

namespace App\Services\User\GroupMappingServices;

use App\DTOs\User\Internal\GroupMappingDTOs\AddGroupMappingDTO;
use App\Exceptions\User\GroupExceptions\GroupMappingValidationException;
use App\Repository\Authentication\ORM\GroupMappingRepository;
use App\Traits\ValidatorProcessorTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddGroupMappingHandler
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private GroupMappingRepository $groupNameMappingRepository;

    public function __construct(ValidatorInterface $validator, GroupMappingRepository $groupNameMappingRepository)
    {
        $this->validator = $validator;
        $this->groupNameMappingRepository = $groupNameMappingRepository;
    }

    /**
     * @throws GroupMappingValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addNewGroupNameMappingEntry(AddGroupMappingDTO $addGroupNameMappingDTO): void
    {
        $newGroupNameMapping = $addGroupNameMappingDTO->getNewGroupMapping();
        $newGroupNameMapping->setUser($addGroupNameMappingDTO->getUserToAddMappingTo());
        $newGroupNameMapping->setGroup($addGroupNameMappingDTO->getGroupToAddUserTo());

        $validationErrors = $this->validator->validate($newGroupNameMapping);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupMappingValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameMappingRepository->persist($newGroupNameMapping);
        $this->groupNameMappingRepository->flush();
    }
}
