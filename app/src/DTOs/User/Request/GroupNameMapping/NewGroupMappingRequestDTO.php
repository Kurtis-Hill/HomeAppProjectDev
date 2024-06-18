<?php

namespace App\DTOs\User\Request\GroupNameMapping;

use Symfony\Component\Validator\Constraints as Assert;

class NewGroupMappingRequestDTO
{
    #[
        Assert\Type(type: 'integer', message: 'userID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "userID cannot be null"
        ),
    ]
    private mixed $userID;

    #[
        Assert\Type(type: 'integer', message: 'groupID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupID cannot be null"
        ),
    ]
    private mixed $groupID;

    public function getUserID(): mixed
    {
        return $this->userID;
    }

    public function setUserID(mixed $userID): void
    {
        $this->userID = $userID;
    }

    public function getGroupID(): mixed
    {
        return $this->groupID;
    }

    public function setGroupID(mixed $groupID): void
    {
        $this->groupID = $groupID;
    }
}
