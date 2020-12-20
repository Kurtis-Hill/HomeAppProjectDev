<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupnNameMapping
 *
 * @ORM\Table(name="groupnnamemapping", indexes={@ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="userID", columns={"userID", "groupNameID"}), @ORM\Index(name="IDX_1C993DEE5FD86D04", columns={"userID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\GroupNameMappingTableRepository")
 */
class GroupnNameMapping
{
    /**
     * @var int
     *
     * @ORM\Column(name="groupNameMappingID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupNameMappingID;

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupNameID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userID;

    /**
     * @return int
     */
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
