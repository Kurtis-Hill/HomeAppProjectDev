<?php

namespace App\User\Services\GroupMappingServices;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\User\Entity\User;
use JetBrains\PhpStorm\ArrayShape;

class GetGroupNameMappingHandler
{
    private GroupNameMappingRepository $groupNameMappingRepository;

    public function __construct(GroupNameMappingRepository $groupNameMappingRepository)
    {
        $this->groupNameMappingRepository = $groupNameMappingRepository;
    }

    #[ArrayShape([GroupNameMapping::class])]
    public function getGroupNameMappingsForUser(User $user): array
    {
        if ($user->isAdmin()) {
            return $this->groupNameMappingRepository->findAll();
        }

        return $this->groupNameMappingRepository->findBy(['user' => $user]);
    }
}
