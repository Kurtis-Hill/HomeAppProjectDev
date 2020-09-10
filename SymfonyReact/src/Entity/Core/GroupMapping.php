<?php


namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupNameMapping
 *
 * @ORM\Table(name="groupnnamemapping", indexes={@ORM\Index(name="userID", columns={"userID"}), @ORM\Index(name="groupNameID", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\GroupNameMappingTableRepository")
 */

class GroupMapping
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userID;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @return int
     */
    public function getGroupNameMappingID(): int
    {
        return $this->groupNameMappingID;
    }


    /**
     * @return int
     */
    public function getGroupnameid(): int
    {
        return $this->groupnameid;
    }

    /**
     * @param int $groupnameid
     */
    public function setGroupnameid(int $groupnameid): void
    {
        $this->groupnameid = $groupnameid;
    }

    /**
     * @return int
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @param int $userID
     */
    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }

}