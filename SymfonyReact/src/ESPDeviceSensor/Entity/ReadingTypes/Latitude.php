<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\LatitudeRepository")
 */
class Latitude extends AbstractReadingType implements AllSensorReadingTypeInterface, StandardReadingSensorInterface
{
    public const READING_TYPE = 'latitude';

    public const HIGH_LATITUDE_READING_BOUNDARY = 90;

    public const LOW_LATITUDE_READING_BOUNDARY = -90;

    public const LATITUDE_SENSORS = [
        SensorType::BMP_SENSOR
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="latitudeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $latitudeID;

    /**
     * @var int|float
     *
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    private int|float $latitude;

    /**
     * @var int|float
     *
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    private int|float $highLatitude = 90;

    /**
     * @var int|float
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private int|float $lowLatitude = -90;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

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
     * @ORM\Column(name="updatedAt", type="date", nullable=false, options={"default"="current_timestamp()"})
     */
    private ?DateTime $time;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    /**
     * @param int $id
     */
    public function setSensorID(int $latitudeId): void
    {
        $this->latitudeID = $latitudeId;
    }

    /**
     * Sensor relational Objects
     */

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

    /**
     * @return float|int
     */
    public function getCurrentReading(): int
    {
        return $this->latitude;
    }

    /**
     * @return float|int
     */
    public function getHighReading(): int
    {
        return $this->highLatitude;
    }

    /**
     * @return int
     */
    public function getLowReading(): int
    {
        return $this->lowLatitude;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param float|int $reading
     */
    public function setCurrentReading(int|float $reading): void
    {
        $this->latitude = $reading;
    }

    /**
     * @param int|float|string $reading
     */
    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highLatitude = $reading;
        }
    }

    /**
     * @param int|float|string $reading
     */
    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowLatitude = $reading;
        }
    }

    /**
     * @param DateTime|null $time
     */
    public function setUpdatedAt(?DateTime $time = null): void
    {
        $this->time = $time ?? new DateTime('now');
    }

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
