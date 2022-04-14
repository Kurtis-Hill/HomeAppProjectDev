<?php

namespace App\UserInterface\Entity\Card;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cardstate
 *
 * @ORM\Table(name="cardstate", uniqueConstraints={@ORM\UniqueConstraint(name="state", columns={"state"})})
 * @ORM\Entity(repositoryClass="App\UserInterface\Repository\ORM\CardRepositories\CardStateRepository")
 */
class Cardstate
{
    public const ON = 'ON';

    public const OFF = 'OFF';

    public const INDEX_ONLY = 'INDEX_ONLY';

    public const ROOM_ONLY = 'ROOM_ONLY';

    public const DEVICE_ONLY = 'DEVICE_ONLY';

    public const ALIAS = 'cardstate';

    private const CARD_STATE_MAX_LENGTH = 20;

    private const CARD_STATE_MIN_LENGTH = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="cardStateID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $cardStateID;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=false)
     */
    #[
        \App\Common\Form\CustomFormValidators\NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::CARD_STATE_MIN_LENGTH,
            max: self::CARD_STATE_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $state;

    /**
     * @return int
     */
    public function getCardstateID(): int
    {
        return $this->cardStateID;
    }

    /**
     * @param int $cardStateID
     */
    public function setCardstateID(int $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

}
