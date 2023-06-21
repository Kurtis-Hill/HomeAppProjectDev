<?php

namespace App\Authentication\Entity;

use App\User\Entity\Group;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Authentication\Repository\ORM\GroupMappingRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: GroupMappingRepository::class),
    ORM\Table(name: "groupmapping"),
    ORM\Index(columns: ["groupID"], name: "groupID"),
    ORM\Index(columns: ["user"], name: "IDX_1C993DEE5FD86D04"),
    ORM\UniqueConstraint(name: "IDX_1C993DEE5FD86D04", columns: ["user", "groupID"]),
]
#[UniqueEntity(fields: ['user', 'groupID'], message: self::GROUP_NAME_MAPPING_EXISTS)]
class GroupMapping
{
    public const GROUP_NAME_MAPPING_EXISTS = 'User is already in this group';

    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
        ORM\Column(name: "groupMappingID", type: "integer", nullable: false)
    ]
    private int $groupMappingID;

    #[
        ORM\ManyToOne(targetEntity: Group::class),
        ORM\JoinColumn(name: "groupID", referencedColumnName: "groupID"),
    ]
    private Group $groupID;

    #[
        ORM\ManyToOne(targetEntity: User::class, inversedBy: "userGroupMappingEntities"),
        ORM\JoinColumn(name: "userID", referencedColumnName: "userID"),
    ]
    private User $user;

    public function getGroupMappingID(): int
    {
        return $this->groupMappingID;
    }

    public function setGroupMappingID(int $groupMappingID): void
    {
        $this->groupMappingID = $groupMappingID;
    }

    public function getGroup(): Group
    {
        return $this->groupID;
    }

    public function setGroup(Group $groupID): void
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
