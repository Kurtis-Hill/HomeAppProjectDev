<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\Internal\GroupDTOs\UpdateGroupDTO;
use App\User\Entity\Group;

class UpdateGroupDTOBuilder
{
    public static function buildUpdateGroupDTO(string $groupName, Group $groupToUpdate): UpdateGroupDTO
    {
        return new UpdateGroupDTO($groupName, $groupToUpdate);
    }
}
