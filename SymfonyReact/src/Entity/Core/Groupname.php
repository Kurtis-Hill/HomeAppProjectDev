<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupname
 *
 * @ORM\Table(name="groupname", uniqueConstraints={@ORM\UniqueConstraint(name="groupName", columns={"groupName"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\GroupNameRepository")
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
     * @return string
     */
    public function getGroupname(): string
    {
        return $this->groupname;
    }

    /**
     * @param string $groupname
     */
    public function setGroupname(string $groupname): void
    {
        $this->groupname = $groupname;
    }

    /**
     * @return \DateTime
     */
    public function getTimez()
    {
        return $this->timez;
    }

    /**
     * @param \DateTime $timez
     */
    public function setTimez($timez): void
    {
        $this->timez = $timez;
    }

}
