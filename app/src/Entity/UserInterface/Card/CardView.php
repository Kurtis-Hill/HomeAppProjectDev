<?php

namespace App\Entity\UserInterface\Card;

use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Entity\UserInterface\Icons;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CardViewRepository::class),
    ORM\Table(name: "cardview"),
    ORM\Index(columns: ["colourID"], name: "FK_E36636B5A356FF88"),
    ORM\Index(columns: ["sensorID"], name: "FK_E36636B53BE475E6"),
    ORM\Index(columns: ["stateID"], name: "FK_E36636B53casrdState"),
    ORM\Index(columns: ["userID"], name: "UserID"),
    ORM\Index(columns: ["iconID"], name: "FK_E36636B5840D9A7A"),
    ORM\Index(columns: ["cardViewID"], name: "cardview_show"),
    ORM\UniqueConstraint(name: "user_cardview", columns: ["userID", "sensorID"]),
]
class CardView
{
    public const ALIAS = 'cardview';

    #[
        ORM\Column(name: "cardViewID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $cardViewID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    #[Assert\NotNull(message: "Sensor cannot be null")]
    private Sensor $sensor;

    #[
        ORM\ManyToOne(targetEntity: CardState::class),
        ORM\JoinColumn(name: "stateID", referencedColumnName: "stateID"),
    ]
    #[Assert\NotNull(message: "CardState state cannot be null")]
    private CardState $cardStateID;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "userID", referencedColumnName: "userID"),
    ]
    #[Assert\NotNull(message: "UserExceptions cannot be null")]
    private ?User $userID;

    #[
        ORM\ManyToOne(targetEntity: Icons::class),
        ORM\JoinColumn(name: "iconID", referencedColumnName: "iconID"),
    ]
    #[Assert\NotNull(message: "Icon cannot be null")]
    private ?Icons $cardIconID;

    #[
        ORM\ManyToOne(targetEntity: Colour::class),
        ORM\JoinColumn(name: "colourID", referencedColumnName: "colourID"),
    ]
    #[Assert\NotNull(message: "Card Colour colour cannot be null")]
    private Colour $cardColourID;

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function setCardViewID(int $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getCardStateID(): CardState
    {
        return $this->cardStateID;
    }

    public function setCardStateID(?CardState $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    public function getUserID(): User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): void
    {
        if ($userID !== null) {
            $this->userID = $userID;
        }
    }

    public function getCardIconID(): Icons
    {
        return $this->cardIconID;
    }

    public function setCardIconID(?Icons $cardIconID): void
    {
        $this->cardIconID = $cardIconID;
    }

    public function getCardColourID(): Colour
    {
        return $this->cardColourID;
    }

    public function setCardColourID(?Colour $cardColourID): void
    {
        $this->cardColourID = $cardColourID;
    }
}
