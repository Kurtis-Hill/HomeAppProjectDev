<?php

namespace App\User\DTO\Request\UserDTOs;

use Symfony\Component\Validator\Constraints as Assert;


class NewUserRequestDTO
{
    #[
        Assert\Type(type: 'string', message: 'firstName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "firstName cannot be null"
        ),
    ]
    private mixed $firstName;

    #[
        Assert\Type(type: 'string', message: 'lastName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "lastName cannot be null"
        ),
    ]
    private mixed $lastName;

    #[
        Assert\Type(type: 'string', message: 'email must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "email cannot be null"
        ),
    ]
    private mixed $email;

    #[
        Assert\Type(type: 'string', message: 'groupName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "groupName cannot be null"
        ),
    ]
    private mixed $groupName;

    #[
        Assert\Type(type: 'string', message: 'password must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "password cannot be null"
        ),
    ]
    private mixed $password;


    #[
        Assert\Type(type: 'array', message: 'roles must be an {{ type }} you have provided {{ value }}'),
    ]
    private mixed $roles = [];

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

    public function getGroupName(): mixed
    {
        return $this->groupName;
    }

    public function setGroupName(mixed $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getPassword(): mixed
    {
        return $this->password;
    }

    public function setPassword(mixed $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): mixed
    {
        return $this->roles;
    }

    public function setRoles(mixed $roles): void
    {
        $this->roles = $roles;
    }
}
