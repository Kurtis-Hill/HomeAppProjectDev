<?php

namespace App\Entity\Card;

use Doctrine\ORM\Mapping as ORM;

/**
 * Icons
 *
 * @ORM\Table(name="icons", uniqueConstraints={@ORM\UniqueConstraint(name="iconName_2", columns={"iconName"})})
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

    /**
     * @return int
     */
    public function getIconid(): int
    {
        return $this->iconid;
    }

    /**
     * @param int $iconid
     */
    public function setIconid(int $iconid): void
    {
        $this->iconid = $iconid;
    }

    /**
     * @return string
     */
    public function getIconname(): string
    {
        return $this->iconname;
    }

    /**
     * @param string $iconname
     */
    public function setIconname(string $iconname): void
    {
        $this->iconname = $iconname;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}
