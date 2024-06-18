<?php

namespace App\Services\User\GroupServices;

use App\DTOs\User\Internal\GroupDTOs\UpdateGroupDTO;
use App\Exceptions\User\GroupExceptions\GroupValidationException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Traits\ValidatorProcessorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateGroupHandler
{
    use ValidatorProcessorTrait;

    private GroupRepositoryInterface $groupNameRepository;

    private ValidatorInterface $validator;

    public function __construct(
        GroupRepositoryInterface $groupNameRepository,
        ValidatorInterface $validator,
    ) {
        $this->groupNameRepository = $groupNameRepository;
        $this->validator = $validator;
    }

    /**
     * @throws \App\Exceptions\User\GroupExceptions\GroupValidationException
     */
    public function updateGroup(UpdateGroupDTO $updateGroupDTO): void
    {
        $groupToUpdate = $updateGroupDTO->getGroupToUpdate();
        if ($updateGroupDTO->getGroupName() !== null) {
            $groupToUpdate->setGroupName($updateGroupDTO->getGroupName());
        }

        $validationErrors = $this->validator->validate($groupToUpdate);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameRepository->flush();
    }
}
