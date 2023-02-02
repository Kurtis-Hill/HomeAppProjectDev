<?php

namespace App\UserInterface\Entity\Card;

use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CardViewRepository::class),
    ORM\Table(name: "cardview"),
    ORM\Index(columns: ["cardColourID"], name: "FK_E36636B5A356FF88"),
    ORM\Index(columns: ["sensorNameID"], name: "FK_E36636B53BE475E6"),
    ORM\Index(columns: ["cardStateID"], name: "FK_E36636B53casrdState"),
    ORM\Index(columns: ["userID"], name: "UserID"),
    ORM\Index(columns: ["cardIconID"], name: "FK_E36636B5840D9A7A"),
    ORM\Index(columns: ["cardViewID"], name: "cardview_show"),
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
        ORM\JoinColumn(name: "sensorNameID", referencedColumnName: "sensorNameID"),
    ]
    #[Assert\NotNull(message: "Sensor cannot be null")]
    private Sensor $sensorNameID;

    #[
        ORM\ManyToOne(targetEntity: Cardstate::class),
        ORM\JoinColumn(name: "cardStateID", referencedColumnName: "cardStateID"),
    ]
    #[Assert\NotNull(message: "CardState state cannot be null")]
    private Cardstate $cardStateID;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "userID", referencedColumnName: "userID"),
    ]
    #[Assert\NotNull(message: "UserExceptions cannot be null")]
    private ?User $userID;

    #[
        ORM\ManyToOne(targetEntity: Icons::class),
        ORM\JoinColumn(name: "cardIconID", referencedColumnName: "iconID"),
    ]
    #[Assert\NotNull(message: "Icon cannot be null")]
    private ?Icons $cardIconID;

    #[
        ORM\ManyToOne(targetEntity: CardColour::class),
        ORM\JoinColumn(name: "cardColourID", referencedColumnName: "colourID"),
    ]
    #[Assert\NotNull(message: "Card Colour colour cannot be null")]
    private CardColour $cardColourID;

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function setCardViewID(int $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorNameID(Sensor $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    public function getCardStateID(): Cardstate
    {
        return $this->cardStateID;
    }

    public function setCardStateID(?Cardstate $cardStateID): void
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

    public function getCardColourID(): CardColour
    {
        return $this->cardColourID;
    }

    public function setCardColourID(?CardColour $cardColourID): void
    {
        $this->cardColourID = $cardColourID;
    }

}
