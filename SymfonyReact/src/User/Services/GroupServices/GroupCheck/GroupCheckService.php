<?php

namespace App\User\Services\GroupServices\GroupCheck;

use App\User\Entity\GroupNames;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Repository\ORM\GroupNameRepositoryInterface;

class GroupCheckService implements GroupCheckServiceInterface
{
    private GroupNameRepositoryInterface $groupNameRepository;

    public function __construct(GroupNameRepositoryInterface $groupNameRepository)
    {
        $this->groupNameRepository = $groupNameRepository;
    }

    public function checkForGroupById(int $groupNameId): GroupNames
    {
        $groupName = $this->groupNameRepository->findOneById($groupNameId);

        if (!$groupName instanceof GroupNames) {
            throw new GroupNameNotFoundException(
                sprintf(
                    GroupNameNotFoundException::MESSAGE,
                    $groupNameId
                )
            );
        }

        return $groupName;
    }
}
