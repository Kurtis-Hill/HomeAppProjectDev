<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Repository\ORM\GroupMappingRepository;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\DTO\Internal\GroupMappingDTOs\AddGroupMappingDTO;
use App\User\Exceptions\GroupExceptions\GroupMappingValidationException;
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
