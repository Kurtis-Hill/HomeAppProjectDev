<?php

namespace App\Entity\Card;

use App\Entity\Core\User;
use App\ESPDeviceSensor\Entity\Sensors;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="cardColour", columns={"cardColourID"}), @ORM\Index(name="cardState", columns={"cardStateID"}), @ORM\Index(name="cardIcon", columns={"cardIconID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"})})
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
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;

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
     * @ORM\ManyToOne(targetEntity="Icons")
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
     * @return Sensors
     */
    public function getSensorNameID(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensors $sensorNameID
     */
    public function setSensorNameID(Sensors $sensorNameID): void
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
