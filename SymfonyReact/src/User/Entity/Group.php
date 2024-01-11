<?php

namespace App\User\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\User\Repository\ORM\GroupRepository;

#[
    ORM\Entity(repositoryClass: GroupRepository::class),
    ORM\Table(name: "groups"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    ORM\UniqueConstraint(name: "groupName", columns: ["groupName"]),
]
#[UniqueEntity(fields: ['groupName'], message: 'Group name already exists')]
class Group
{
    public const HOME_APP_GROUP_NAME = 'home-app-group';

    public const ADMIN_GROUP_NAME = 'admin-group';

    private const GROUP_NAME_MIN_LENGTH = 2;

    private const GROUP_NAME_MAX_LENGTH = 50;

    public function __toString(): string
    {
        return $this->getGroupName();
    }

    #[
        ORM\Column(name: "groupID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $groupID;

    #[
        ORM\Column(name: "groupName", type: "string", length: 50, nullable: false),
    ]
    #[
        Assert\Length(
            min: self::GROUP_NAME_MIN_LENGTH,
            max: self::GROUP_NAME_MAX_LENGTH,
            minMessage: "Group name must be at least {{ limit }} characters long",
            maxMessage: "Group name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $groupName;

    #[
        ORM\Column(
            name: "createdAt",
            type: "datetime",
            nullable: false,
        ),
    ]
    private DateTimeInterface $createdAt;

    public function getGroupID(): int
    {
        return $this->groupID;
    }

    public function setGroupID(int $groupID): void
    {
        $this->groupID = $groupID;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

}
