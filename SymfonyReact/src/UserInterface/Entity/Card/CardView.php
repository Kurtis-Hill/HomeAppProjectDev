<?php

namespace App\UserInterface\Entity\Card;

use App\ESPDeviceSensor\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\Entity\Icons;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", indexes={@ORM\Index(name="FK_E36636B5A356FF88", columns={"cardColourID"}), @ORM\Index(name="FK_E36636B53BE475E6", columns={"sensorNameID"}), @ORM\Index(name="FK_E36636B53casrdState", columns={"cardStateID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="FK_E36636B5840D9A7A", columns={"cardIconID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardViewRepository")
 */
class CardView
{
    public const ALIAS = 'cardview';

    /**
     * @var int
     *
     * @ORM\Column(name="cardViewID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $cardViewID;

    /**
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    #[Assert\NotNull(message: "Sensor Object cannot be null")]
    private Sensor $sensorNameID;

    /**
     * @var Cardstate
     *
     * @ORM\ManyToOne(targetEntity="App\UserInterface\Entity\Card\Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    #[Assert\NotNull(message: "CardState Object cannot be null")]
    private Cardstate $cardStateID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    #[Assert\NotNull(message: "User Object cannot be null")]
    private User $userID;

    /**
     * @var Icons
     *
     * @ORM\ManyToOne(targetEntity="App\UserInterface\Entity\Icons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardIconID", referencedColumnName="iconID")
     * })
     */
    #[Assert\NotNull(message: "Icon Object cannot be null")]
    private Icons $cardIconID;

    /**
     * @var CardColour
     *
     * @ORM\ManyToOne(targetEntity="App\UserInterface\Entity\Card\CardColour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardColourID", referencedColumnName="colourID")
     * })
     */
    #[Assert\NotNull(message: "CardColour Object cannot be null")]
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

    public function setCardStateID(Cardstate $cardStateID): void
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

    public function setCardIconID(Icons $cardIconID): void
    {
        $this->cardIconID = $cardIconID;
    }

    public function getCardColourID(): CardColour
    {
        return $this->cardColourID;
    }

    public function setCardColourID(CardColour $cardColourID): void
    {
        $this->cardColourID = $cardColourID;
    }

}
