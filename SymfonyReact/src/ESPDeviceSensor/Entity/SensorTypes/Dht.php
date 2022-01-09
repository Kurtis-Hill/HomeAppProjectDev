<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorService;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="dhtsensor", uniqueConstraints={@ORM\UniqueConstraint(name="cardviewID", columns={"sensorNameID"}), @ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\SensorType\DhtRepository")
 */
class Dht implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface
{
    public const NAME = 'Dht';

    public const ALIAS = 'dht';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 80;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -40;

    public const SENSOR_TYPE_ID = 1;

    /**
     * @ORM\Column(name="dhtID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $dhtID;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

    private CardView $cardView;

    public function getSensorTypeID(): int
    {
        return $this->dhtID;
    }

    public function setSensorTypeID(int $dhtID): void
    {
        $this->dhtID = $dhtID;
    }

    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorObject(Sensor $id): void
    {
        $this->sensorNameID = $id;
    }

    public function getHumidObject(): Humidity
    {
        return $this->humidID;
    }

    public function setHumidObject(Humidity $humidID): void
    {
        $this->humidID = $humidID;
    }

    public function getCardViewObject(): ?CardView
    {
        return $this->cardView;
    }

    public function setCardViewObject(CardView $cardView): void
    {
        $this->cardView = $cardView;
    }

    public function getMaxTemperature(): float|int
    {
        return self::HIGH_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMinTemperature(): float|int
    {
        return self::LOW_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMaxHumidity(): float|int
    {
        return Humidity::HIGH_READING;
    }

    public function getMinHumidity(): float|int
    {
        return Humidity::LOW_READING;
    }

    public function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public function getSensorTypeAlias(): string
    {
        return self::ALIAS;
    }


    public function getSensorClass(): string
    {
        return self::class;
    }
}
