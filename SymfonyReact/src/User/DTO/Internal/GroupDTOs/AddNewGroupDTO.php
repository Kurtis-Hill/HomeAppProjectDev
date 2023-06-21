<?php

namespace App\User\DTO\Internal\GroupDTOs;

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
