<?php

namespace App\User\DTO\InternalDTOs\GroupDTOs;

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
