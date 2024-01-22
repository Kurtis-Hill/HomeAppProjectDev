<?php

namespace App\User\Services\GroupServices;

use App\User\Entity\Group;
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

    #[ArrayShape([Group::class])]
    /**
     * @return Group[]
     */
    public function getUsersGroups(User $user): array
    {
        return $user->isAdmin()
            ? $this->groupNameRepository->findAll()
            : $user->getAssociatedGroups();
    }
}
