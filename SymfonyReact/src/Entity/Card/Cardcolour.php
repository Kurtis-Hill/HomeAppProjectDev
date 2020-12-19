<?php

namespace App\Entity\Card;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardcolour
 *
 * @ORM\Table(name="cardcolour", uniqueConstraints={@ORM\UniqueConstraint(name="colour", columns={"colour"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardcolourRepository")
 */
class Cardcolour
{
    /**
     * @var int
     *
     * @ORM\Column(name="colourID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $colourid;

    /**
     * @var string
     *
     * @ORM\Column(name="colour", type="string", length=20, nullable=false)
     */
    private $colour;

    /**
     * @var string
     *I
     * @ORM\Column(name="shade", type="string", length=20, nullable=false)
     */
    private $shade;

    /**
     * @return int
     */
    public function getColourid(): int
    {
        return $this->colourid;
    }

    /**
     * @param int $colourid
     */
    public function setColourid(int $colourid): void
    {
        $this->colourid = $colourid;
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
