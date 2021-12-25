<?php

namespace App\Entity\New;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupname
 *
 * @ORM\Table(name="groupname", uniqueConstraints={@ORM\UniqueConstraint(name="groupName", columns={"groupName"})})
 * @ORM\Entity
 */
class Groupname
{
    /**
     * @var int
     *
     * @ORM\Column(name="groupNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $groupnameid;

    /**
     * @var string
     *
     * @ORM\Column(name="groupName", type="string", length=50, nullable=false)
     */
    private $groupname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $timez = 'current_timestamp()';


}
