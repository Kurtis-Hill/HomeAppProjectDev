<?php

namespace App\User\Services\GroupServices\GroupCheck;

use App\Entity\Core\GroupNames;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;

interface GroupCheckServiceInterface
{
    /**
     * @param int $groupNameId
     * @return GroupNames
     * @throws GroupNameNotFoundException
     */
    public function checkForGroupById(int $groupNameId): GroupNames;
}
