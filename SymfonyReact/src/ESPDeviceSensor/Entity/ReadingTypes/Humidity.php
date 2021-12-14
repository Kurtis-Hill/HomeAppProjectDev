<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Humidity
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"}), @ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\HumidityRepository")
 */
class Humidity extends AbstractReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'humidity';

    public const READING_SYMBOL = '%';

    public const HIGH_READING = 100;

    public const LOW_READING = 0;

    public const HUMIDITY_SENSORS = [
        SensorType::BMP_SENSOR,
        SensorType::DHT_SENSOR
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $humidID;

    /**
     * @var int
     *
     * @ORM\Column(name="humidReading", type="integer", precision=10, scale=0, nullable=false)
     */
    #[
        Assert\LessThan(
            value: self::LOW_READING,
            message: "Humidity reading must not be less than {{ value }}"
        ),
        Assert\GreaterThan(
            value: self::HIGH_READING,
            message: "Humidity reading must not be greater than {{ value }}"
        )
    ]
    private int $currentReading;

    /**
     * @var int
     *
     * @ORM\Column(name="highHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    #[
        Assert\LessThan(
            value: self::LOW_READING,
            message: "Humidity reading must not be less than {{ value }}"
        ),
        Assert\GreaterThan(
            value: self::HIGH_READING,
            message: "Humidity reading must not be greater than {{ value }}"
        )
    ]
    private int $highHumid = 80;

    /**
     * @var int
     *
     * @ORM\Column(name="lowHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    #[
        Assert\LessThan(
            value: self::LOW_READING,
            message: "Humidity reading must not be less than {{ value }}"
        ),
        Assert\GreaterThan(
            value: self::HIGH_READING,
            message: "Humidity reading must not be greater than {{ value }}"
        )
    ]
    private int $lowHumid = 10;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private ?DateTime $time;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->humidID;
    }

    public function setSensorID(int $id): void
    {
        $this->humidID = $id;
    }

    /**
     * @return Sensors
     */
    public function getSensorObject(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensors $id
     */
    public function setSensorNameID(Sensors $id): void
    {
        $this->sensorNameID = $id;
    }

    /**
     * Sensor Reading Methods
     */

    public function getCurrentReading(): int|float
    {
        return $this->currentReading;
    }

    /**
     * @return int|float
     */
    public function getHighReading(): int|float
    {
        return $this->highHumid;
    }

    /**
     * @return float|null
     */
    public function getLowReading(): int|float
    {
        return $this->lowHumid;
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
     * @param int|float $reading
     */
    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highHumid = $reading;
        }
    }

    /**
     * @param int|float|string $reading
     */
    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowHumid = $reading;
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


    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }


    public function getSensorTypeName(): string
    {
        return self::READING_TYPE;
    }
}
