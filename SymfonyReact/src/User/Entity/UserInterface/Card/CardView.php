<?php

namespace App\User\Entity\UserInterface\Card;

use App\Entity\Core\User;
use App\ESPDeviceSensor\Entity\Sensor;
use App\User\Entity\UserInterface\Icons;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", indexes={@ORM\Index(name="FK_E36636B5A356FF88", columns={"cardColourID"}), @ORM\Index(name="FK_E36636B53BE475E6", columns={"sensorNameID"}), @ORM\Index(name="FK_E36636B53casrdState", columns={"cardStateID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="FK_E36636B5840D9A7A", columns={"cardIconID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardViewRepository")
 */
class CardView
{
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
    private Sensor $sensorNameID;

    /**
     * @var Cardstate
     *
     * @ORM\ManyToOne(targetEntity="Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    private Cardstate $cardStateID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private User $userID;

    /**
     * @var Icons
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\UserInterface\Icons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardIconID", referencedColumnName="iconID")
     * })
     */
    private Icons $cardIconID;

    /**
     * @var CardColour
     *
     * @ORM\ManyToOne(targetEntity="CardColour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardColourID", referencedColumnName="colourID")
     * })
     */
    private CardColour $cardColourID;

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    /**
     * @param int $cardViewID
     */
    public function setCardViewID(int $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    /**
     * @return Sensor
     */
    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $sensorNameID
     */
    public function setSensorNameID(Sensor $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    /**
     * @return Cardstate
     */
    public function getCardStateID(): Cardstate
    {
        return $this->cardStateID;
    }

    /**
     * @param Cardstate $cardStateID
     */
    public function setCardStateID(Cardstate $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    /**
     * @return User
     */
    public function getUserID(): User
    {
        return $this->userID;
    }

    /**
     * @param User|null $userID
     */
    public function setUserID(?User $userID): void
    {
        if ($userID !== null) {
            $this->userID = $userID;
        }
    }

    /**
     * @return Icons
     */
    public function getCardIconID(): Icons
    {
        return $this->cardIconID;
    }

    /**
     * @param Icons $cardIconID
     */
    public function setCardIconID(Icons $cardIconID): void
    {
        $this->cardIconID = $cardIconID;
    }

    /**
     * @return CardColour
     */
    public function getCardColourID(): CardColour
    {
        return $this->cardColourID;
    }

    /**
     * @param CardColour $cardColourID
     */
    public function setCardColourID(CardColour|int $cardColourID): void
    {
        $this->cardColourID = $cardColourID;
    }

}
