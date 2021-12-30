<?php

namespace App\UserInterface\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Form\CustomFormValidators as NoSpecialCharacters;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Icons
 *
 * @ORM\Table(name="icons", uniqueConstraints={@ORM\UniqueConstraint(name="iconName_2", columns={"iconName"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\IconRepository")
 */
class Icons
{
    private const ICON_NAME_MAX_LENGTH = 20;

    private const ICON_NAME_MIN_LENGTH = 2;

    private const ICON_DESCRIPTION_MAX_LENGTH = 20;

    private const ICON_DESCRIPTION_MIN_LENGTH = 2;

    public const ALIAS = 'icons';
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
    #[
        NoSpecialCharacters\NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::ICON_NAME_MIN_LENGTH,
            max: self::ICON_NAME_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $iconName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=20, nullable=false)
     */
    #[
        NoSpecialCharacters\NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::ICON_DESCRIPTION_MIN_LENGTH,
            max: self::ICON_DESCRIPTION_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
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
