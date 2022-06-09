<?php

namespace App\UserInterface\Entity\Card;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CardStateRepository::class),
    ORM\Table(name: "cardstate"),
    ORM\UniqueConstraint(name: "State", columns: ["state"]),
]
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

    #[
        ORM\Column(name: "cardStateID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $cardStateID;

    #[
        ORM\Column(name: "state", type: "string", length: 50, nullable: false),
    ]
    #[
        NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::CARD_STATE_MIN_LENGTH,
            max: self::CARD_STATE_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $state;

    public function getCardstateID(): int
    {
        return $this->cardStateID;
    }

    public function setCardstateID(int $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

}
