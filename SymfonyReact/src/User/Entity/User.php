<?php

namespace App\User\Entity;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Repository\ORM\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\Table(name: "user"),
    ORM\UniqueConstraint(name: "email", columns: ["email"]),
    ORM\Index(columns: ["groupNameID"], name: "GroupName"),
    ORM\UniqueConstraint(name: "email", columns: ["email"]),
]
#[UniqueEntity('email')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[
        ORM\Column(name: "userID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $userID;

    #[
        ORM\Column(name: "firstName", type: "string", length: 20, nullable: false),
    ]
    private string $firstName;

    #[
        ORM\Column(name: "lastName", type: "string", length: 20, nullable: false),
    ]
    private string $lastName;

    #[
        ORM\Column(name: "email", type: "string", length: 180, nullable: false),
    ]
    private string $email;

    #[
        ORM\Column(name: "roles", type: "json", nullable: false),
    ]
    private array $roles;

    #[
        ORM\Column(name: "profilePic", type: "string", length: 100, nullable: true, options: ["default" => "/assets/pictures/guest.jpg"]),
    ]
    private string $profilePic = '/assets/pictures/guest.jpg';

    #[
        ORM\Column(name: "password", type: "text", length: 0, nullable: false),
    ]
    private string $password;

    #[
        ORM\ManyToOne(targetEntity: GroupNames::class),
        ORM\JoinColumn(name: "groupNameID", referencedColumnName: "groupNameID"),
    ]
    private GroupNames|int $groupNameID;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" => "current_timestamp()"]),
    ]
    private DateTime $createdAt;

    #[ArrayShape([GroupNameMapping::class])]
    private array $userGroupMappingEntities = [];

    #[ArrayShape([GroupNameMapping::class])]
    public function getUserGroupMappingEntities(): array
    {
        return $this->userGroupMappingEntities;
    }

    public function setUserGroupMappingEntities(array $userGroupMappingEntities): void
    {
        $this->userGroupMappingEntities = $userGroupMappingEntities;
    }

    #[ArrayShape([GroupNames::class])]
    public function getGroupNameObjects(): array
    {
        foreach ($this->userGroupMappingEntities as $groupName) {
            $groupNameArray[] = $groupName->getGroupNameID();
        }

        return $groupNameArray ?? [];
    }

    #[ArrayShape(['int'])]
    public function getGroupNameIds(): array
    {
        $groupNames = [];

        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroupNameID()->getGroupNameID();
        }

        return $groupNames;
    }

    #[ArrayShape(['groupNameID' => 'int'])]
    public function getGroupNameAndIds(): array
    {
        $groupNames = [];
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = ['groupNameID' => $entity->getGroupNameID()->getGroupNameID(), 'groupName' => $entity->getGroupNameID()->getGroupName()];
        }

        return $groupNames;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    #[ArrayShape(['ROLES'])]
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles ?? ['ROLE_USER'];

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserID(): ?int
    {
        return $this->userID;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePic(string $profilePic): self
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function setSalt(string $salt): self
    {
        return $this;
    }

    public function getGroupNameID(): GroupNames
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(int|GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt = null): self
    {
        $this->createdAt = $createdAt ?? new DateTime('now');

        return $this;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->email;
    }
}
