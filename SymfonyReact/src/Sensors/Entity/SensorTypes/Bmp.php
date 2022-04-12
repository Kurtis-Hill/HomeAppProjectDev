<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bmp
 *
 * @ORM\Table(name="bmp", uniqueConstraints={@ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="latitudeID", columns={"latitudeID"}), @ORM\UniqueConstraint(name="tempID*", columns={"tempID"})}, indexes={@ORM\Index(name="bmp_ibfk_1", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\SensorType\BmpRepository")
 */
class Bmp implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface, HumiditySensorTypeInterface, LatitudeSensorTypeInterface
{
    public const NAME = 'Bmp';

    public const ALIAS = 'bmp';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 85;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -45;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
        Latitude::READING_TYPE
    ];

    /**
     * @ORM\Column(name="bmpID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $bmpID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Humidity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private Humidity $humidID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Latitude")
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

    public function getMaxLatitude(): float|int
    {
        return Latitude::HIGH_READING;
    }

    public function getMinLatitude(): float|int
    {
        return Latitude::LOW_READING;
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

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
