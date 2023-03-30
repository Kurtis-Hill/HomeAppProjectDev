<?php

namespace App\User\DTO\RequestDTOs\GroupNameMapping;

use Symfony\Component\Validator\Constraints as Assert;

class NewGroupNameMappingRequestDTO
{
    #[
        Assert\Type(type: 'integer', message: 'userID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "userID cannot be null"
        ),
    ]
    private mixed $userID;

    #[
        Assert\Type(type: 'integer', message: 'groupNameID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupNameID cannot be null"
        ),
    ]
    private mixed $groupNameID;

    public function getUserID(): mixed
    {
        return $this->userID;
    }

    public function setUserID(mixed $userID): void
    {
        $this->userID = $userID;
    }

    public function getGroupNameID(): mixed
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(mixed $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }
}
