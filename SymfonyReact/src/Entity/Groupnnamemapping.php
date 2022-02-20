<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupnnamemapping
 *
 * @ORM\Table(name="groupnnamemapping", indexes={@ORM\Index(name="userID", columns={"userID", "groupNameID"}), @ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="IDX_1C993DEE5FD86D04", columns={"userID"})})
 * @ORM\Entity
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
     * @var \Groupname
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;


}
