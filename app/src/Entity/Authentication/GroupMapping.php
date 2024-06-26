<?php
declare(strict_types=1);

namespace App\Entity\Authentication;

use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Authentication\ORM\GroupMappingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: GroupMappingRepository::class),
    ORM\Table(name: "groupmappings"),
    ORM\Index(columns: ["groupID"], name: "groupID"),
    ORM\Index(columns: ["userID"], name: "IDX_1C993DEE5FD86D04"),
    ORM\UniqueConstraint(name: "IDX_1C993DEE5FD86D05", columns: ["userID", "groupID"]),
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
