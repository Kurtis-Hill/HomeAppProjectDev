<?php

namespace App\User\Entity;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\User\Repository\ORM\GroupNameRepository;

#[UniqueEntity(fields: ['groupName'], message: 'Group name already exists')]
#[
    ORM\Entity(repositoryClass: GroupNameRepository::class),
    ORM\Table(name: "groupname"),
    ORM\Index(columns: ["createdBy"], name: "createdBy"),
    ORM\Index(columns: ["roomID"], name: "roomID"),

]
class GroupNames
{
    private const GROUP_NAME_MIN_LENGTH = 2;

    private const GROUP_NAME_MAX_LENGTH = 50;

    public function __toString(): string
    {
        return $this->getGroupName();
    }

    #[
        ORM\Column(name: "groupNameID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $groupNameID;

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
        ORM\Column(name: "createdAt", type: "datetime", nullable: false, options: ["default" =>"current_timestamp()"]),
    ]
    private DateTimeInterface $createdAt;

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(int $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
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
