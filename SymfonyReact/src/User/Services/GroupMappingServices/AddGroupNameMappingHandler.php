<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameMappingValidationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddGroupNameMappingHandler
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
    public function addNewGroupNameMappingEntry(GroupNames $groupName, User $user): void
    {
        $newGroupNameMapping = new GroupNameMapping();
        $newGroupNameMapping->setUser($user);
        $newGroupNameMapping->setGroupName($groupName);

        $validationErrors = $this->validator->validate($newGroupNameMapping);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupNameMappingValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameMappingRepository->persist($newGroupNameMapping);
        $this->groupNameMappingRepository->flush();
    }
}
