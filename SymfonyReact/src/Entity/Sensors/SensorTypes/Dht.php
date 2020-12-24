<?php

namespace App\Entity\Sensors\SensorTypes;

use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorType;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dht
 *
 * @ORM\Table(name="dhtsensor", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="cardviewID", columns={"cardviewID"})})
 * @ORM\Entity
 */
class Dht implements StandardSensorTypeInterface, TemperatureSensorType, HumiditySensorType
{
    /**
     * @var int
     *
     * @ORM\Column(name="dhtID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $dhtID;

    /**
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @var CardView
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardviewID", referencedColumnName="cardViewID")
     * })
     */
    private CardView $cardViewID;

    /**
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->dhtID;
    }

    /**
     * @param int $dhtID
     */
    public function setSensorTypeID(int $dhtID): void
    {
        $this->dhtID = $dhtID;
    }

    /**
     * @return int
     */
    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    /**
     * @param Temperature $tempID
     */
    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    /**
     * @return CardView
     */
    public function getCardViewObject(): Cardview
    {
        return $this->cardViewID;
    }

    /**
     * @param Cardview $cardViewID
     */
    public function setCardViewObject(Cardview $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    /**
     * @return Humidity
     */
    public function getHumidObject(): Humidity
    {
        return $this->humidID;
    }

    /**
     * @param Humidity $humidID
     */
    public function setHumidObject(Humidity $humidID): void
    {
        $this->humidID = $humidID;
    }


}
