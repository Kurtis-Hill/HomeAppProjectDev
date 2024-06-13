<?php

namespace App\Builders\User\GroupName;

use App\DTOs\User\Internal\GroupDTOs\AddNewGroupDTO;

class AddNewGroupNameDTOBuilder
{
    public static function buildAddNewGroupDTO(string $groupName): AddNewGroupDTO
    {
        return new AddNewGroupDTO(
            $groupName,
        );
    }
}
