<?php

namespace App\User\Services\GroupNameServices;

use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use JetBrains\PhpStorm\ArrayShape;

class UserGroupsFinder
{
    private GroupRepository $groupNameRepository;

    public function __construct(GroupRepository $groupNameRepository)
    {
        $this->groupNameRepository = $groupNameRepository;
    }

    #[ArrayShape([GroupNames::class])]
    public function getGroupNamesForUser(User $user): array
    {
        return $user->isAdmin()
            ? $this->groupNameRepository->findAll()
            : $user->getAssociatedGroupNames();
    }
}
