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
    ORM\Index(columns: ["groupID"], name: "groupID"),
    ORM\Index(columns: ["user"], name: "IDX_1C993DEE5FD86D04"),
    ORM\UniqueConstraint(name: "IDX_1C993DEE5FD86D04", columns: ["user", "groupID"]),
]
#[UniqueEntity(fields: ['user', 'groupID'], message: self::GROUP_NAME_MAPPING_EXISTS)]
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
        ORM\JoinColumn(name: "groupID", referencedColumnName: "groupID"),
    ]
    private GroupNames $groupID;

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

    public function getGroupID(): GroupNames
    {
        return $this->groupID;
    }

    public function setGroupID(GroupNames $groupID): void
    {
        $this->groupID = $groupID;
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
