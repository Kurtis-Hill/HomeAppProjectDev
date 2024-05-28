<?php

namespace App\UserInterface\Entity\Card;

use App\Common\CustomValidators\NoSpecialCharactersNameConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepository;

#[
    ORM\Entity(repositoryClass: CardColourRepository::class),
    ORM\Table(name: "colours"),
    ORM\UniqueConstraint(name: "colour", columns: ["colour"]),
    ORM\UniqueConstraint(name: "shade", columns: ["shade"]),
]
#[UniqueEntity('colour')]
class Colour
{
    private const COLOUR_MAX_LENGTH = 20;

    private const COLOUR_MIN_LENGTH = 2;

    private const SHADE_MAX_LENGTH = 20;

    private const SHADE_MIN_LENGTH = 2;

    public const ALIAS = 'cardcolour';

    #[
        ORM\Column(name: "colourID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $colourID;

    #[
        ORM\Column(name: "colour", type: "string", length: 20, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::COLOUR_MIN_LENGTH,
            max: self::COLOUR_MAX_LENGTH,
            minMessage: "Colour must be at least {{ limit }} characters long",
            maxMessage: "Colour cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $colour;

    #[
        ORM\Column(name: "shade", type: "string", length: 20, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::SHADE_MIN_LENGTH,
            max: self::SHADE_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $shade;

    public function getColourID(): int
    {
        return $this->colourID;
    }

    public function setColourID(int $colourID): void
    {
        $this->colourID = $colourID;
    }

    public function getColour(): string
    {
        return $this->colour;
    }

    public function setColour(string $colour): void
    {
        $this->colour = $colour;
    }

    public function getShade(): string
    {
        return $this->shade;
    }

    public function setShade(string $shade): void
    {
        $this->shade = $shade;
    }
}
