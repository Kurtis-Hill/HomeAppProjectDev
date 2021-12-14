<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\User\Entity\UserInterface\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="analogID", columns={"analogID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"})})
 * @ORM\Entity
 */
class Soil implements SensorInterface, StandardSensorTypeInterface, AnalogSensorTypeInterface
{
    public const NAME = 'Soil';

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
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private Analog $analogID;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensors $sensorNameID;

    private CardView $cardView;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->soilID;
    }

    /**
     * @param int $soilID
     */
    public function setSensorTypeID(int $soilID): void
    {
        $this->soilID = $soilID;
    }

    /**
     * @return Analog
     */
    public function getAnalogObject(): Analog
    {
        return $this->analogID;
    }

    /**
     * @param Analog $analogID
     */
    public function setAnalogObject(Analog $analogID): void
    {
        $this->analogID = $analogID;
    }

    /**
     * @return Sensors
     */
    public function getSensorObject(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensors $sensor
     */
    public function setSensorObject(Sensors $sensor): void
    {
        $this->sensorNameID = $sensor;
    }

    /**
     * @return CardView|null
     */
    public function getCardViewObject(): ?CardView
    {
        return $this->cardView;
    }

    /**
     * @param CardView $cardView
     */
    public function setCardViewObject(CardView $cardView): void
    {
        $this->cardView = $cardView;
    }

    public function getSensorNameID(): Sensors
    {
        return $this->sensorNameID;
    }


}
