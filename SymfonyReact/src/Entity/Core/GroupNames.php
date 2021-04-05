<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupNames.
 *
 * @ORM\Table(name="groupname", uniqueConstraints={@ORM\UniqueConstraint(name="groupName", columns={"groupName"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\GroupNameRepository")
 */
class GroupNames
{
    /**
     * @var int
     *
     * @ORM\Column(name="groupNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $groupNameID;

    /**
     * @var string
     *
     * @ORM\Column(name="groupName", type="string", length=50, nullable=false)
     */
    private string $groupName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $time;

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(int $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(?\DateTime $time = null): void
    {
        $this->time = $time === null ?  new \DateTime('now') : $time;
    }
}
