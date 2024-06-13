<?php

namespace App\Entity\UserInterface;

use App\CustomValidators\NoSpecialCharactersNameConstraint;
use App\Repository\UserInterface\ORM\IconsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: IconsRepository::class),
    ORM\Table(name: "icons"),
    ORM\UniqueConstraint(name: "iconName_2", columns: ["iconName"]),
]
class Icons
{
    private const ICON_NAME_MAX_LENGTH = 20;

    private const ICON_NAME_MIN_LENGTH = 2;

    private const ICON_DESCRIPTION_MAX_LENGTH = 20;

    private const ICON_DESCRIPTION_MIN_LENGTH = 2;

    public const ALIAS = 'icons';

    #[
        ORM\Column(name: "iconID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $iconID;

    #[
        ORM\Column(name: "iconName", type: "string", length: 20, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::ICON_NAME_MIN_LENGTH,
            max: self::ICON_NAME_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $iconName;

    #[
        ORM\Column(name: "description", type: "string", length: 20, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::ICON_DESCRIPTION_MIN_LENGTH,
            max: self::ICON_DESCRIPTION_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $description;

    public function getIconID(): int
    {
        return $this->iconID;
    }

    public function setIconID(int $iconID): void
    {
        $this->iconID = $iconID;
    }

    public function getIconName(): string
    {
        return $this->iconName;
    }

    public function setIconName(string $iconName): void
    {
        $this->iconName = $iconName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }



}
