<?php

namespace App\User\Builders\GroupName;

use App\User\DTO\Internal\GroupDTOs\UpdateGroupDTO;
use App\User\Entity\GroupNames;

class UpdateGroupDTOBuilder
{
    public static function buildUpdateGroupDTO(string $groupName, GroupNames $groupToUpdate): UpdateGroupDTO
    {
        return new UpdateGroupDTO($groupName, $groupToUpdate);
    }
}
