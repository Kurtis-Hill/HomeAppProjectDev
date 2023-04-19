<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Entity\GroupMapping;
use App\Authentication\Repository\ORM\GroupMappingRepository;
use App\User\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class GetGroupNameMappingHandler
{
    private GroupMappingRepository $groupNameMappingRepository;

    public function __construct(GroupMappingRepository $groupNameMappingRepository)
    {
        $this->groupNameMappingRepository = $groupNameMappingRepository;
    }

    #[ArrayShape([GroupMapping::class])]
    public function getGroupNameMappingsForUser(User $user): array
    {
        if ($user->isAdmin()) {
            return $this->groupNameMappingRepository->findAll();
        }

        return $this->groupNameMappingRepository->findBy(['user' => $user]);
    }
}
