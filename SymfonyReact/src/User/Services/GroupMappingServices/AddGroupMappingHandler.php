<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\DTO\Internal\GroupNameMappingDTOs\AddGroupMappingDTO;
use App\User\Exceptions\GroupNameExceptions\GroupNameMappingValidationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddGroupMappingHandler
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private GroupNameMappingRepository $groupNameMappingRepository;

    public function __construct(ValidatorInterface $validator, GroupNameMappingRepository $groupNameMappingRepository)
    {
        $this->validator = $validator;
        $this->groupNameMappingRepository = $groupNameMappingRepository;
    }

    /**
     * @throws GroupNameMappingValidationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addNewGroupNameMappingEntry(AddGroupMappingDTO $addGroupNameMappingDTO): void
    {
        $newGroupNameMapping = $addGroupNameMappingDTO->getNewGroupNameMapping();
        $newGroupNameMapping->setUser($addGroupNameMappingDTO->getUserToAddMappingTo());
        $newGroupNameMapping->setGroupID($addGroupNameMappingDTO->getGroupToAddUserTo());

        $validationErrors = $this->validator->validate($newGroupNameMapping);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupNameMappingValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameMappingRepository->persist($newGroupNameMapping);
        $this->groupNameMappingRepository->flush();
    }
}
