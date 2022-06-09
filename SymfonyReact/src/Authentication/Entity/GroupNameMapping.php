<?php

namespace App\Authentication\Entity;

use App\User\Entity\GroupNames;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Authentication\Repository\ORM\GroupNameMappingTableRepository;

#[
    ORM\Entity(repositoryClass: GroupNameMappingTableRepository::class),
    ORM\Table(name: "groupnnamemapping"),
    ORM\Index(columns: ["groupNameID"], name: "groupNameID"),
    ORM\Index(columns: ["userID", "groupNameID"], name: "userID"),
    ORM\Index(columns: ["userID"], name: "IDX_1C993DEE5FD86D04"),
]
class GroupNameMapping
{
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
    private GroupNames $groupNameID;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "userID", referencedColumnName: "userID"),
    ]
    private User $userID;

    public function getGroupNameMappingID(): int
    {
        return $this->groupNameMappingID;
    }

    /**
     * @param int $groupNameMappingID
     */
    public function setGroupNameMappingID(int $groupNameMappingID): void
    {
        $this->groupNameMappingID = $groupNameMappingID;
    }

    /**
     * @return GroupNames
     */
    public function getGroupNameID(): GroupNames
    {
        return $this->groupNameID;
    }

    /**
     * @param GroupNames $groupNameID
     */
    public function setGroupNameID(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    /**
     * @return User
     */
    public function getUserID(): User
    {
        return $this->userID;
    }

    /**
     * @param User $userID
     */
    public function setUserID(User $userID): void
    {
        $this->userID = $userID;
    }

}
