<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupnnamemapping
 *
 * @ORM\Table(name="groupnnamemapping", indexes={@ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="userID", columns={"userID", "groupNameID"}), @ORM\Index(name="IDX_1C993DEE5FD86D04", columns={"userID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\GroupNameMappingTableRepository")
 */
class Groupnnamemapping
{
    /**
     * @var int
     *
     * @ORM\Column(name="groupNameMappingID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupnamemappingid;

    /**
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;

    /**
     * @return int
     */
    public function getGroupnamemappingid(): int
    {
        return $this->groupnamemappingid;
    }

    /**
     * @param int $groupnamemappingid
     */
    public function setGroupnamemappingid(int $groupnamemappingid): void
    {
        $this->groupnamemappingid = $groupnamemappingid;
    }

    /**
     * @return Groupname
     */
    public function getGroupnameid(): Groupname
    {
        return $this->groupnameid;
    }

    /**
     * @param Groupname $groupnameid
     */
    public function setGroupnameid(Groupname $groupnameid): void
    {
        $this->groupnameid = $groupnameid;
    }

    /**
     * @return User
     */
    public function getUserid(): User
    {
        return $this->userid;
    }

    /**
     * @param User $userid
     */
    public function setUserid(User $userid): void
    {
        $this->userid = $userid;
    }

}
