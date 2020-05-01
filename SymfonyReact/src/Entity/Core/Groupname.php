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
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timez = 'CURRENT_TIMESTAMP';

    public function getGroupnameid(): ?int
    {
        return $this->groupnameid;
    }

    public function getGroupname(): ?string
    {
        return $this->groupname;
    }

    public function setGroupname(string $groupname): self
    {
        $this->groupname = $groupname;

        return $this;
    }

    public function getTimez(): ?\DateTimeInterface
    {
        return $this->timez;
    }

    public function setTimez(\DateTimeInterface $timez): self
    {
        $this->timez = $timez;

        return $this;
    }


}
