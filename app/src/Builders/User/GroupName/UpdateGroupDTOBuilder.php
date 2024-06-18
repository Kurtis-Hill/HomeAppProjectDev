<?php

namespace App\Builders\User\GroupName;

use App\DTOs\User\Internal\GroupDTOs\UpdateGroupDTO;
use App\Entity\User\Group;

class UpdateGroupDTOBuilder
{
    public static function buildUpdateGroupDTO(string $groupName, Group $groupToUpdate): UpdateGroupDTO
    {
        return new UpdateGroupDTO($groupName, $groupToUpdate);
    }
}
