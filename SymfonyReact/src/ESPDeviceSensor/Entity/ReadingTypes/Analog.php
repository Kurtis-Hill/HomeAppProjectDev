<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Analog
 *
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="analog_ibfk_3", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\AnalogRepository")
 */
class Analog extends AbstractReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'analog';

    public const ANALOG_SENSORS = [
        SensorType::SOIL_SENSOR
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $analogID;

    /**
     * @var float
     *
     * @ORM\Column(name="analogReading", type="smallint", nullable=true, options={"default"="NULL"})
     */
    private float $analogReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    private int $highAnalog = 9999;

    /**
     * @var int
     *
     * @ORM\Column(name="lowAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    private int $lowAnalog = 1111;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true)
     */
    private bool $constRecord = false;

    /**
     * @var DateTime
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
        return $this->analogID;
    }


    public function setSensorID(int $analogid): void
    {
        $this->analogID = $analogid;
    }


    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $sensorNameID
     */
    public function setSensorObject(Sensor $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    /**
     * Sensor Reading Methods
     */

    /**
     * @return int
     */
    public function getCurrentReading(): int|float
    {
        return $this->analogReading;
    }

    /**
     * @return int|float
     */
    public function getHighReading(): int|float
    {
        return $this->highAnalog;
    }

    /**
     * @return int|float
     */
    public function getLowReading(): int|float
    {
        return $this->lowAnalog;
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
        $this->analogReading = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highAnalog = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowAnalog = $reading;
        }
    }

    public function setUpdatedAt(?DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt ?? new DateTime('now');
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
