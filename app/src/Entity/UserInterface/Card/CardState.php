<?php

namespace App\Entity\UserInterface\Card;

use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepository;
use App\CustomValidators\NoSpecialCharactersNameConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CardStateRepository::class),
    ORM\Table(name: "state"),
    ORM\UniqueConstraint(name: "state", columns: ["state"]),
]
class CardState
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
        ORM\Column(name: "stateID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $stateID;

    #[
        ORM\Column(name: "state", type: "string", length: 50, nullable: false),
    ]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::CARD_STATE_MIN_LENGTH,
            max: self::CARD_STATE_MAX_LENGTH,
            minMessage: "Colour shade must be at least {{ limit }} characters long",
            maxMessage: "Colour shade cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $state;

    public function getStateID(): int
    {
        return $this->stateID;
    }

    public function setStateID(int $stateID): void
    {
        $this->stateID = $stateID;
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
