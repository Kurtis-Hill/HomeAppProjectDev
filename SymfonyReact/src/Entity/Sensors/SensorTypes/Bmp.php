<?php

namespace App\Entity\Sensors\SensorTypes;

use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Bmp
 *
 * @ORM\Table(name="bmp", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="latitudeID", columns={"latitudeID"})})
 * @ORM\Entity
 */
class Bmp implements StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface, LatitudeSensorTypeInterface
{
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensors $sensorNameID;

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
     * @var Humidity
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @var Latitude
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Latitude")
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
