<?php

namespace App\User\DTO\Request\UserDTOs;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequestDTO
{
    #[
        Assert\Type(type: ['string', 'null'], message: 'firstName must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $firstName = null;

    #[
        Assert\Type(type: ['string', 'null'], message: 'lastName must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $lastName = null;

    #[
        Assert\Type(type: ['string', 'null'], message: 'email must be a {{ type }} you have provided {{ value }}'),
//        Assert\Email(
//            message: "email must be a valid email address"
//        ),
    ]
    private mixed $email = null;

    #[
        Assert\Type(type: ['array', 'null'], message: 'roles must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $roles = null;

    #[
        Assert\Type(type: ['string', 'null'], message: 'newPassword must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $newPassword = null;

    #[
        Assert\Type(type: ['string', 'null'], message: 'oldPassword must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $oldPassword = null;

    #[
        Assert\Type(type: ['int', 'null'], message: 'groupID must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $groupID = null;

    public function getFirstName(): mixed
    {
        return $this->firstName;
    }

    public function setFirstName(mixed $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): mixed
    {
        return $this->lastName;
    }

    public function setLastName(mixed $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): mixed
    {
        return $this->email;
    }

    public function setEmail(mixed $email): void
    {
        $this->email = $email;
    }

    public function getRoles(): mixed
    {
        return $this->roles;
    }

    public function setRoles(mixed $roles): void
    {
        $this->roles = $roles;
    }

    public function getNewPassword(): mixed
    {
        return $this->newPassword;
    }

    public function setNewPassword(mixed $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getOldPassword(): mixed
    {
        return $this->oldPassword;
    }

    public function setOldPassword(mixed $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
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
