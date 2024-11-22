<?php

namespace App\Services\User\GroupServices;

use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\User\ORM\GroupRepository;
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

    public function getGroupIDs(User $user): array
    {
        $groups = $this->getUsersGroups($user);
        return array_map(static function ($group) {
            return $group->getGroupID();
        }, $groups);
    }
}
