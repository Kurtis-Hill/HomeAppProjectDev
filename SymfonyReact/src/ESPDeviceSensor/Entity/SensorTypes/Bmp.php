<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\NoReturn;

/**
 * Bmp
 *
 * @ORM\Table(name="bmp", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="latitudeID", columns={"latitudeID"})})
 * @ORM\Entity
 */
class Bmp implements SensorInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface, LatitudeSensorTypeInterface
{
    public const NAME = 'Bmp';

    public const HIGH_TEMPERATURE_READING_BOUNDRY = 85;

    public const LOW_TEMPERATURE_READING_BOUNDRY = -45;

    /**
     * @var int
     *
     * @ORM\Column(name="bmpID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $bmpID;

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
     * @var Latitude
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Latitude")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="latitudeID", referencedColumnName="latitudeID")
     * })
     */
    private Latitude $latitudeID;

    /**
     * @var CardView
     */
    private CardView $cardView;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->bmpID;
    }

    /**
     * @param int $bmpID
     */
    public function setSensorTypeID(int $bmpID): void
    {
        $this->bmpID = $bmpID;
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
     * @return Sensors
     */
    public function getSensorNameID(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @return Temperature
     */
    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    /**
     * @param Temperature $tempID
     */
    #[NoReturn]
    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
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
     * @return Latitude
     */
    public function getLatitudeObject(): Latitude
    {
        return $this->latitudeID;
    }

    /**
     * @param Latitude $latitudeID
     */
    public function setLatitudeObject(Latitude $latitudeID): void
    {
        $this->latitudeID = $latitudeID;
    }

    public function getCardViewObject(): ?CardView
    {
        return $this->cardView;
    }

    public function setCardViewObject(CardView $cardView): void
    {
        $this->cardView = $cardView;
    }


}
