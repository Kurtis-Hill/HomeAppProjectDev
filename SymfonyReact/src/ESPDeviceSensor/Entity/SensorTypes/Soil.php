<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\User\Entity\UserInterface\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="analogID", columns={"analogID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Soil implements SensorTypeInterface, StandardSensorTypeInterface, AnalogSensorTypeInterface
{
    public const NAME = 'Soil';

    private const HIGH_SOIL_READING_BOUNDARY = 9999;

    private const LOW_SOIL_READING_BOUNDARY = 0;

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
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

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
     * @return Sensor
     */
    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $id
     */
    public function setSensorObject(Sensor $id): void
    {
        $this->sensorNameID = $id;
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


    public function getMaxAnalog(): float|int
    {
        return self::HIGH_SOIL_READING_BOUNDARY;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_SOIL_READING_BOUNDARY;
    }

    public function getSensorTypeName(): string
    {
        return self::NAME;
    }
}
