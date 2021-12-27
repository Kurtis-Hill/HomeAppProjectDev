<?php

namespace App\UserInterface\Entity;

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
    private int $iconID;

    /**
     * @var string
     *
     * @ORM\Column(name="iconName", type="string", length=20, nullable=false)
     */
    private string $iconName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=20, nullable=false)
     */
    private string $description;

    /**
     * @return int
     */
    public function getIconID(): int
    {
        return $this->iconID;
    }

    /**
     * @param int $iconID
     */
    public function setIconID(int $iconID): void
    {
        $this->iconID = $iconID;
    }

    /**
     * @return string
     */
    public function getIconName(): string
    {
        return $this->iconName;
    }

    /**
     * @param string $iconName
     */
    public function setIconName(string $iconName): void
    {
        $this->iconName = $iconName;
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
