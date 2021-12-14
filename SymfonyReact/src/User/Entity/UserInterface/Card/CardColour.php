<?php

namespace App\User\Entity\UserInterface\Card;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardcolour.
 *
 * @ORM\Table(name="cardcolour", uniqueConstraints={@ORM\UniqueConstraint(name="colour", columns={"colour"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardColourRepository")
 */
class CardColour
{
    /**
     * @var int
     *
     * @ORM\Column(name="colourID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $colourID;

    /**
     * @var string
     *
     * @ORM\Column(name="colour", type="string", length=20, nullable=false)
     */
    private string $colour;

    /**
     * @var string
     *I
     * @ORM\Column(name="shade", type="string", length=20, nullable=false)
     */
    private string $shade;

    /**
     * @return int
     */
    public function getColourID(): int
    {
        return $this->colourID;
    }

    /**
     * @param int $colourID
     */
    public function setColourID(int $colourID): void
    {
        $this->colourID = $colourID;
    }

    /**
     * @return string
     */
    public function getColour(): string
    {
        return $this->colour;
    }

    /**
     * @param string $colour
     */
    public function setColour(string $colour): void
    {
        $this->colour = $colour;
    }

    /**
     * @return string
     */
    public function getShade(): string
    {
        return $this->shade;
    }

    /**
     * @param string $shade
     */
    public function setShade(string $shade): void
    {
        $this->shade = $shade;
    }
}
