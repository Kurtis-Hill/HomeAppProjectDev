<?php

namespace App\User\Services\GroupNameServices;

use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;

class GetGroupNamesHandler
{
    private GroupNameRepository $groupNameRepository;

    public function __construct(GroupNameRepository $groupNameRepository)
    {
        $this->groupNameRepository = $groupNameRepository;
    }

    public function getGroupNameDataForUser(User $user): array
    {
        return $user->isAdmin()
            ? $this->groupNameRepository->findAll()
            : $user->getAssociatedGroupNames();
    }
}
