<?php

namespace App\Entity\Sensors\SensorTypes;

use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bmp
 *
 * @ORM\Table(name="bmp", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="latitudeID", columns={"latitudeID"})})
 * @ORM\Entity
 */
class Bmp
{
    /**
     * @var int
     *
     * @ORM\Column(name="bmpID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $bmpID;

    /**
     * @var CardView
     *
     * @ORM\ManyToOne(targetEntity="Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardViewID", referencedColumnName="cardViewID")
     * })
     */
    private CardView $cardViewID;

    /**
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @var Latitude
     *
     * @ORM\ManyToOne(targetEntity="Latitude")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="latitudeID", referencedColumnName="latitudeID")
     * })
     */
    private Latitude $latitudeID;

    /**
     * @return int
     */
    public function getBmpID(): int
    {
        return $this->bmpID;
    }

    /**
     * @param int $bmpID
     */
    public function setBmpID(int $bmpID): void
    {
        $this->bmpID = $bmpID;
    }

    /**
     * @return CardView
     */
    public function getCardViewID(): CardView
    {
        return $this->cardViewID;
    }

    /**
     * @param CardView $cardViewID
     */
    public function setCardViewID(CardView $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    /**
     * @return Temperature
     */
    public function getTempID(): Temperature
    {
        return $this->tempID;
    }

    /**
     * @param Temperature $tempID
     */
    public function setTempID(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    /**
     * @return Humidity
     */
    public function getHumidID(): Humidity
    {
        return $this->humidID;
    }

    /**
     * @param Humidity $humidID
     */
    public function setHumidID(Humidity $humidID): void
    {
        $this->humidID = $humidID;
    }

    /**
     * @return Latitude
     */
    public function getLatitudeID(): Latitude
    {
        return $this->latitudeID;
    }

    /**
     * @param Latitude $latitudeID
     */
    public function setLatitudeID(Latitude $latitudeID): void
    {
        $this->latitudeID = $latitudeID;
    }


}
