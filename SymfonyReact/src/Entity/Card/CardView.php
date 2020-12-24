<?php

namespace App\Entity\Card;

use App\Entity\Core\User;
use App\Entity\Sensors\Sensors;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="cardColour", columns={"cardColourID"}), @ORM\Index(name="cardState", columns={"cardStateID"}), @ORM\Index(name="cardIcon", columns={"cardIconID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardviewRepository")
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
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
    public function getSensorObject(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensors $sensorNameID
     */
    public function setSensorObject(Sensors $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    /**
     * @return Cardstate
     */
    public function getCardStateObject(): Cardstate
    {
        return $this->cardStateID;
    }

    /**
     * @param Cardstate $cardStateID
     */
    public function setCardStateObject(Cardstate $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    /**
     * @return User
     */
    public function getUserObject(): User
    {
        return $this->userID;
    }

    /**
     * @param User $userID
     */
    public function setUserObject(User $userID): void
    {
        $this->userID = $userID;
    }

    /**
     * @return Icons
     */
    public function getCardIconObject(): Icons
    {
        return $this->cardIconID;
    }

    /**
     * @param Icons $cardIconID
     */
    public function setCardIconObject(Icons $cardIconID): void
    {
        $this->cardIconID = $cardIconID;
    }

    /**
     * @return CardColour
     */
    public function getCardColourObject(): CardColour
    {
        return $this->cardColourID;
    }

    /**
     * @param CardColour $cardColourID
     */
    public function setCardColourObject(CardColour $cardColourID): void
    {
        $this->cardColourID = $cardColourID;
    }

}
