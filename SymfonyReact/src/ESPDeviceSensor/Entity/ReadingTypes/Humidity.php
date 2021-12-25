<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
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
        HumidityConstraint
//        Assert\LessThan(
//            value: self::LOW_READING,
//            message: "Humidity reading must not be less than {{ value }}"
//        ),
//        Assert\GreaterThan(
//            value: self::HIGH_READING,
//            message: "Humidity reading must not be greater than {{ value }}"
//        )
    ]
    private int $currentReading;

    /**
     * @var int
     *
     * @ORM\Column(name="highHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    #[
        HumidityConstraint
//        Assert\LessThan(
//            value: self::LOW_READING,
//            message: "Humidity reading must not be less than {{ value }}"
//        ),
//        Assert\GreaterThan(
//            value: self::HIGH_READING,
//            message: "Humidity reading must not be greater than {{ value }}"
//        )
    ]
    private int $highHumid = 80;

    /**
     * @var int
     *
     * @ORM\Column(name="lowHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    #[
        HumidityConstraint
//        Assert\LessThan(
//            value: self::LOW_READING,
//            message: "Humidity reading must not be less than {{ value }}"
//        ),
//        Assert\GreaterThan(
//            value: self::HIGH_READING,
//            message: "Humidity reading must not be greater than {{ value }}"
//        )
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
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensor $sensorNameID;


    public function getSensorID(): int
    {
        return $this->humidID;
    }

    public function setSensorID(int $id): void
    {
        $this->humidID = $id;
    }

    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorNameID(Sensor $id): void
    {
        $this->sensorNameID = $id;
    }

    public function getCurrentReading(): int|float
    {
        return $this->currentReading;
    }

    public function getHighReading(): int|float
    {
        return $this->highHumid;
    }

    public function getLowReading(): int|float
    {
        return $this->lowHumid;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->time;
    }

    public function setCurrentReading(int|float $reading): void
    {
        $this->currentReading = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highHumid = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowHumid = $reading;
        }
    }

    public function setUpdatedAt(?DateTime $time = null): void
    {
        $this->time = $time ?? new DateTime('now');
    }


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
