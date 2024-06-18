<?php

namespace App\Services\User\GroupMappingServices;

use App\Entity\Authentication\GroupMapping;
use App\Entity\User\User;
use App\Repository\Authentication\ORM\GroupMappingRepository;
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
