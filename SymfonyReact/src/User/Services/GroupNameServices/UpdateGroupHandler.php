<?php

namespace App\User\Services\GroupNameServices;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\DTO\Internal\GroupDTOs\UpdateGroupDTO;
use App\User\Exceptions\GroupNameExceptions\GroupNameValidationException;
use App\User\Repository\ORM\GroupRepositoryInterface;
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
     * @throws GroupNameValidationException
     */
    public function updateGroup(UpdateGroupDTO $updateGroupDTO): void
    {
        $groupToUpdate = $updateGroupDTO->getGroupToUpdate();
        if ($updateGroupDTO->getGroupName() !== null) {
            $groupToUpdate->setGroupName($updateGroupDTO->getGroupName());
        }

        $validationErrors = $this->validator->validate($groupToUpdate);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            throw new GroupNameValidationException($this->getValidationErrorAsArray($validationErrors));
        }

        $this->groupNameRepository->flush();
    }
}
