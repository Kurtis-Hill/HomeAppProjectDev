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

    public function getColourid(): ?int
    {
        return $this->colourid;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(string $colour): self
    {
        $this->colour = $colour;

        return $this;
    }


}
