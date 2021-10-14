<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Dht
 *
 * @ORM\Table(name="dhtsensor", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="cardviewID", columns={"cardviewID"})})
 * @ORM\Entity
 */
class Dht implements SensorInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface
{
    public const HIGH_TEMPERATURE_READING_BOUNDRY = 80;

    public const LOW_TEMPERATURE_READING_BOUNDRY = -40;

    public const SENSOR_TYPE_ID = 1;

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
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensors $sensorNameID;

    /**
     * @var CardView
     */
    private CardView $cardView;

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
     * @return CardView
     */
    public function getSensorNameID(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensors $sensor
     */
    public function setSensorNameID(Sensors $sensor): void
    {
        $this->sensorNameID = $sensor;
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
}
