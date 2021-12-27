<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Temp
 *
 * @ORM\Table(name="temp", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\TemperatureRepository")
 */
class Temperature extends AbstractReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'temperature';

    public const READING_SYMBOL = '°C';

    public const TEMPERATURE_SENSORS = [
      SensorType::BMP_SENSOR,
      SensorType::DALLAS_TEMPERATURE,
      SensorType::DHT_SENSOR
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $tempID;

    /**
     * @var float
     *
     * @ORM\Column(name="tempReading", type="float", precision=10, scale=0, nullable=false)
     */
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        )
    ]
    private float $currentReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="26"})
     */
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        )
    ]
    private float $highTemp = 50;

    /**
     * @var float
     *
     * @ORM\Column(name="lowTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="12"})
     */
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        )
    ]
    private float $lowTemp = 10;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTime $updatedAt;

    /**
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensor $sensorNameID;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->tempID;
    }

    /**
     * @param int $id
     */
    public function setSensorID(int $id): void
    {
        $this->tempID = $id;
    }

    /**
     * @return Sensor
     */
    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $id
     */
    public function setSensorNameID(Sensor $id): void
    {
        $this->sensorNameID = $id;
    }

    /**
     * Sensor Reading Methods
     */
    #[Pure] public function getCurrentReading(): int|float
    {
        return round($this->currentReading, 2);
    }

    /**
     * @return int|float
     */
    public function getHighReading(): int|float
    {
        return $this->highTemp;
    }

    /**
     * @return int|float
     */
    public function getLowReading(): int|float
    {
        return $this->lowTemp;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param int|float $reading
     */
    public function setCurrentReading(int|float $reading): void
    {
        $this->currentReading = $reading;
    }

    /**
     * @param int|float|string $reading
     */
    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highTemp = $reading;
        }
    }

    /**
     * @param int|float|string $reading
     */
    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowTemp = $reading;
        }
    }

    /**
     * @param DateTime|null $time
     */
    public function setUpdatedAt(?DateTime $time = null): void
    {
        $this->updatedAt = $time ?? new DateTime('now');
    }

    /**
     * Sensor Functional Methods
     */

    /**
     * @return bool
     */
    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    /**
     * @param bool $constRecord
     */
    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }


    public function getSensorTypeName(): string
    {
        return self::READING_TYPE;
    }
}
