<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Icons
 *
 * @ORM\Table(name="icons", uniqueConstraints={@ORM\UniqueConstraint(name="iconName_2", columns={"iconName"})}, indexes={@ORM\Index(name="IconName", columns={"iconName"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\IconRepository")
 */
class Icons
{
    /**
     * @var int
     *
     * @ORM\Column(name="iconID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iconid;

    /**
     * @var string
     *
     * @ORM\Column(name="iconName", type="string", length=20, nullable=false)
     */
    private $iconname;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=20, nullable=false)
     */
    private $description;

    public function getIconid(): ?int
    {
        return $this->iconid;
    }

    public function getIconname(): ?string
    {
        return $this->iconname;
    }

    public function setIconname(string $iconname): self
    {
        $this->iconname = $iconname;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
