<?php

namespace App\DTOs\User\Internal\GroupDTOs;

readonly class AddNewGroupDTO
{
    public function __construct(
        private string $groupName,
    ) {
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
