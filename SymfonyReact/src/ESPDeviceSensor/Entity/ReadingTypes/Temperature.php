<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Temp
 *
 * @ORM\Table(name="temp", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="temp_ibfk_6", columns={"deviceNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="GroupName", columns={"groupNameID"})})
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
        Assert\LessThan(
        value: Dht::LOW_TEMPERATURE_READING_BOUNDRY,
        message: 'Temperature settings for Dht sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
        groups: [Dht::NAME]
        ),
        Assert\GreaterThan(
            value: Dht::HIGH_TEMPERATURE_READING_BOUNDRY,
            message:  'Temperature settings for Dht sensor cannot be below '. Dht::LOW_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dht::NAME]
        ),
    ]
    #[
        Assert\LessThan(
            value: Dallas::LOW_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Dallas sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dallas::NAME]
        ),
        Assert\GreaterThan(
            value: Dallas::HIGH_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Dallas sensor cannot exceed ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dallas::NAME]
        ),
    ]
    #[
        Assert\LessThan(
            value: Bmp::LOW_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Bmp sensor cannot be below '. Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Bmp::NAME]
        ),
        Assert\GreaterThan(
            value: Bmp::HIGH_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Bmp sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Bmp::NAME]
        ),
    ]
    private float $currentReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="26"})
     */
    #[
        Assert\GreaterThan(
            value: Dht::HIGH_TEMPERATURE_READING_BOUNDRY,
            message:  'Temperature settings for Dht sensor cannot be below '. Dht::LOW_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dht::NAME]
        ),
        Assert\GreaterThan(
            value: Dallas::HIGH_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Dallas sensor cannot exceed ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dallas::NAME]
        ),
        Assert\GreaterThan(
            value: Bmp::HIGH_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Bmp sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Bmp::NAME]
        ),
    ]
    private float $highTemp = 50;

    /**
     * @var float
     *
     * @ORM\Column(name="lowTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="12"})
     */
    #[
        Assert\LessThan(
            value: Dht::LOW_TEMPERATURE_READING_BOUNDRY,
            message: 'Temperature settings for Dht sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dht::NAME]
        ),
        Assert\LessThan(
            value: Dallas::LOW_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Dallas sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Dallas::NAME]
        ),
        Assert\LessThan(
            value: Bmp::LOW_TEMPERATURE_READING_BOUNDARY,
            message: 'Temperature settings for Bmp sensor cannot be below '. Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL,
            groups: [Bmp::NAME]
        ),
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
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTime $time;

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
     * @param int $tempID
     */
    public function setSensorID(int $tempID): void
    {
        $this->tempID = $tempID;
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
    public function getTime(): \DateTimeInterface
    {
        return $this->time;
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
    public function setTime(?DateTime $time = null): void
    {
        $this->time = $time ?? new DateTime('now');
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
