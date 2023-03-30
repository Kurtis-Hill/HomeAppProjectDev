<?php

namespace App\Authentication\Entity;

use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: GroupNameMappingRepository::class),
    ORM\Table(name: "groupnnamemapping"),
    ORM\Index(columns: ["groupName"], name: "groupName"),
    ORM\Index(columns: ["user"], name: "IDX_1C993DEE5FD86D04"),
    ORM\UniqueConstraint(name: "IDX_1C993DEE5FD86D04", columns: ["user", "groupName"]),
]
#[UniqueEntity(fields: ['user', 'groupName'], message: self::GROUP_NAME_MAPPING_EXISTS)]
class GroupNameMapping
{
    public const GROUP_NAME_MAPPING_EXISTS = 'User is already in this group';

    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
        ORM\Column(name: "groupNameMappingID", type: "integer", nullable: false)
    ]
    private int $groupNameMappingID;

    #[
        ORM\ManyToOne(targetEntity: GroupNames::class),
        ORM\JoinColumn(name: "groupNameID", referencedColumnName: "groupNameID"),
    ]
    private GroupNames $groupName;

    #[
        ORM\ManyToOne(targetEntity: User::class, inversedBy: "userGroupMappingEntities"),
        ORM\JoinColumn(name: "userID", referencedColumnName: "userID"),
    ]
    private User $user;

    public function getGroupNameMappingID(): int
    {
        return $this->groupNameMappingID;
    }

    public function setGroupNameMappingID(int $groupNameMappingID): void
    {
        $this->groupNameMappingID = $groupNameMappingID;
    }

    public function getGroupName(): GroupNames
    {
        return $this->groupName;
    }

    public function setGroupName(GroupNames $groupNameID): void
    {
        $this->groupName = $groupNameID;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $userID): void
    {
        $this->user = $userID;
    }

}
