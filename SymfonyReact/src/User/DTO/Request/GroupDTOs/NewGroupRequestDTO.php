<?php

namespace App\User\DTO\Request\GroupDTOs;

use Symfony\Component\Validator\Constraints as Assert;

class NewGroupRequestDTO
{
    #[
        Assert\Type(type: 'string', message: 'groupName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupName cannot be null"
        ),
    ]
    private mixed $groupName;

    public function setGroupName(mixed $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getGroupName(): mixed
    {
        return $this->groupName;
    }
}
