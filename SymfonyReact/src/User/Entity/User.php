<?php

namespace App\User\Entity;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Repository\ORM\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ORM\Table(name: "user"),
    ORM\UniqueConstraint(name: "email", columns: ["email"]),
    ORM\Index(columns: ["groupNameID"], name: "GroupName"),
    ORM\UniqueConstraint(name: "email", columns: ["email"]),
    ORM\Index(columns: ["profilePic"], name: "profilePic"),
]
#[UniqueEntity(fields: ['email'], message: 'Email already exists')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const USER_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];
    public const DEFAULT_PROFILE_PICTURE = 'guest.jpg';

    #[
        ORM\Column(name: "userID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $userID;

    #[
        ORM\Column(name: "firstName", type: "string", length: 20, nullable: false),
        Assert\Length(
            min: 2,
            max: 20,
            minMessage: 'First name must be at least {{ limit }} characters long',
            maxMessage: 'First name cannot be longer than {{ limit }} characters',
        ),
        Assert\NotBlank,
    ]
    private string $firstName;

    #[
        ORM\Column(name: "lastName", type: "string", length: 20, nullable: false),
        Assert\Length(
            min: 2,
            max: 20,
            minMessage: 'Last name must be at least {{ limit }} characters long',
            maxMessage: 'Last name cannot be longer than {{ limit }} characters',
        ),
        Assert\NotBlank,
    ]
    private string $lastName;

    #[
        ORM\Column(name: "email", type: "string", length: 180, nullable: false),
        Assert\Length(
            min: 5,
            max: 180,
            minMessage: 'First name must be at least {{ limit }} characters long',
            maxMessage: 'First name cannot be longer than {{ limit }} characters',
        ),
        Assert\NotBlank,
        Assert\Email,

    ]
    private string $email;

    #[
        ORM\Column(name: "roles", type: "json", nullable: false),
    ]
    private array $roles;

    #[
        ORM\Column(name: "profilePic", type: "string", length: 100, nullable: true, options: ["default" => "/assets/pictures/guest.jpg"]),
    ]
    private string $profilePic = self::DEFAULT_PROFILE_PICTURE;

    #[
        ORM\Column(name: "password", type: "text", length: 0, nullable: false),
        Assert\NotCompromisedPassword,
        Assert\Length(
            min: 8,
            max: 255,
            minMessage: 'Password must be at least {{ limit }} characters long',
            maxMessage: 'Password cannot be longer than {{ limit }} characters',
        ),
//        Assert\NotBlank,
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
    private DateTimeInterface $createdAt;

    #[
        ArrayShape([GroupNameMapping::class]),
        ORM\OneToMany(mappedBy: "user", targetEntity: GroupNameMapping::class),
    ]
    private Selectable|array $userGroupMappingEntities;

    public function __construct()
    {
        $this->userGroupMappingEntities = new ArrayCollection();
    }

    #[ArrayShape([GroupNameMapping::class])]
    public function getUserGroupMappingEntities(): ArrayCollection|Selectable
    {
        return $this->userGroupMappingEntities;
    }

    public function setUserGroupMappingEntities(array $userGroupMappingEntities): void
    {
        $this->userGroupMappingEntities = $userGroupMappingEntities;
    }

    public function getUsersGroupName(): GroupNames
    {
        return $this->groupNameID;
    }

    #[ArrayShape([GroupNames::class])]
    public function getGroupNameMappings(): array
    {
        /** @var GroupNameMapping $groupName */
        foreach ($this->userGroupMappingEntities as $groupName) {
            $groupNameArray[] = $groupName->getGroupName();
        }

        return $groupNameArray ?? [];
    }

    #[ArrayShape(['int'])]
    public function getAssociatedGroupNameIds(): array
    {
        $groupNames[] = $this->getGroupNameID()->getGroupNameID();
        /** @var GroupNameMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroupName()->getGroupNameID();
        }
        return $groupNames;
    }

    #[ArrayShape(['groupNameID' => 'int', 'groupName' => 'string'])]
    public function getAssociatedGroupNameAndIds(): array
    {
        $groupNames[] = [
            'groupNameID' => $this->getGroupNameID()->getGroupNameID(),
            'groupName' => $this->getGroupNameID()->getGroupName()
        ];
        /** @var GroupNameMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = [
                'groupNameID' => $entity->getGroupName()->getGroupNameID(),
                'groupName' => $entity->getGroupName()->getGroupName()
            ];
        }

        return $groupNames;
    }

    #[ArrayShape([GroupNames::class])]
    public function getAssociatedGroupNames(): array
    {
        $groupNames[] = $this->getGroupNameID();
        /** @var GroupNameMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroupName();
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

    public function setRoles(array $roles = []): self
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt = null): self
    {
        $this->createdAt = $createdAt ?? new DateTime('now');

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }
}
