<?php

namespace App\User\Entity;

use App\Authentication\Entity\GroupMapping;
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
    ORM\Table(name: "users"),
    ORM\UniqueConstraint(name: "email", columns: ["email"]),
    ORM\Index(columns: ["groupID"], name: "GroupName"),
    ORM\Index(columns: ["profilePic"], name: "profilePic"),
    ORM\Index(columns: ["roles"], name: "roles"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
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

    public const USER_TYPE = 'user';

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
        Assert\Choice(
            choices: self::USER_ROLES,
            multiple: true,
            message: 'Choose a valid role.',
            multipleMessage: 'Choose at least one valid role.'
        ),
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
    ]
    private string $password;

    #[
        ORM\ManyToOne(targetEntity: Group::class),
        ORM\JoinColumn(name: "groupID", referencedColumnName: "groupID"),
    ]
    private Group|int $groupID;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false,
//            options: ["default" => "current_timestamp()"]
        ),
    ]
    private DateTimeInterface $createdAt;

    #[
        ArrayShape([GroupMapping::class]),
        ORM\OneToMany(mappedBy: "user", targetEntity: GroupMapping::class),
    ]
    private Selectable|array $userGroupMappingEntities;

    public function __construct()
    {
        $this->userGroupMappingEntities = new ArrayCollection();
    }

    #[ArrayShape([GroupMapping::class])]
    public function getUserGroupMappingEntities(): ArrayCollection|Selectable
    {
        return $this->userGroupMappingEntities;
    }

    public function setUserGroupMappingEntities(array $userGroupMappingEntities): void
    {
        $this->userGroupMappingEntities = $userGroupMappingEntities;
    }

    #[ArrayShape(['int'])]
    public function getAssociatedGroupIDs(): array
    {
        $groupNames[] = $this->getGroup()->getGroupID();
        /** @var GroupMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroup()->getGroupID();
        }
        return $groupNames;
    }

    #[ArrayShape(['groupID' => 'int', 'groupName' => 'string'])]
    public function getAssociatedGroupNameAndIds(): array
    {
        $groupNames[] = [
            'groupID' => $this->getGroup()->getGroupID(),
            'groupName' => $this->getGroup()->getGroupName()
        ];
        /** @var GroupMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = [
                'groupID' => $entity->getGroup()->getGroupID(),
                'groupName' => $entity->getGroup()->getGroupName()
            ];
        }

        return $groupNames;
    }

    #[ArrayShape([Group::class])]
    public function getAssociatedGroups(): array
    {
        $groupNames[] = $this->getGroup();
        /** @var GroupMapping $entity */
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroup();
        }
        return $groupNames;
    }

    public function getUsersGroupName(): ?Group
    {
        foreach ($this->userGroupMappingEntities as $groupMapping) {
            if ($groupMapping->getGroup()->getCreatedBy()->getUserID() === $this->getUserID()) {
                return $groupMapping->getGroup();
            }
        }

        return null;
    }

    #[ArrayShape([Group::class])]
    public function getGroupMappings(): array
    {
        /** @var GroupMapping $groupName */
        foreach ($this->userGroupMappingEntities as $groupName) {
            $groupNameArray[] = $groupName->getGroup();
        }

        return $groupNameArray ?? [];
    }

    #[ArrayShape(['ROLES'])]
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles ?? [self::ROLE_USER];

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

    public function getGroup(): Group
    {
        return $this->groupID;
    }

    public function setGroup(int|Group $groupID): void
    {
        $this->groupID = $groupID;
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
        return in_array(self::ROLE_ADMIN, $this->getRoles(), true);
    }
}
