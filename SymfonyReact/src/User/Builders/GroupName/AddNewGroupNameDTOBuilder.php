<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\InternalDTOs\GroupDTOs\AddNewGroupDTO;

class AddNewGroupNameDTOBuilder
{
    public static function buildAddNewGroupDTO(string $groupName): AddNewGroupDTO
    {
        return new AddNewGroupDTO(
            $groupName,
        );
    }
}
