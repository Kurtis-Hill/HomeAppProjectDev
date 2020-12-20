<?php

namespace App\Entity\Sensors\SensorTypes;

use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Analog;
use Doctrine\ORM\Mapping as ORM;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="analogID", columns={"analogID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"})})
 * @ORM\Entity
 */
class Soil
{
    /**
     * @var int
     *
     * @ORM\Column(name="soilID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $soilID;

    /**
     * @var Analog
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private Analog $analogID;

    /**
     * @var CardView
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardViewID", referencedColumnName="cardViewID")
     * })
     */
    private CardView $cardViewID;

    /**
     * @return int
     */
    public function getSoilID(): int
    {
        return $this->soilID;
    }

    /**
     * @param int $soilID
     */
    public function setSoilID(int $soilID): void
    {
        $this->soilID = $soilID;
    }

    /**
     * @return Analog
     */
    public function getAnalogID(): Analog
    {
        return $this->analogID;
    }

    /**
     * @param Analog $analogID
     */
    public function setAnalogID(Analog $analogID): void
    {
        $this->analogID = $analogID;
    }

    /**
     * @return Cardview
     */
    public function getCardViewID(): Cardview
    {
        return $this->cardViewID;
    }

    /**
     * @param Cardview $cardViewID
     */
    public function setCardViewID(Cardview $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }


}
