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
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="analog_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="analog_ibfk_6", columns={"deviceNameID"})})
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
     * @ORM\Column(name="analogReading", type="smallint", precision=10, scale=0, nullable=false)
     */
    private float $analogReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highAnalog", type="smallint", precision=10, scale=0, nullable=false, options={"default"="1111"})
     */
    private int $highAnalog = 9999;

    /**
     * @var int
     *
     * @ORM\Column(name="lowAnalog", type="smallint", precision=10, scale=0, nullable=false, options={"default"="2222"})
     */
    private int $lowAnalog = 1111;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     * @var DateTime
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
        return $this->analogID;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->analogID = $analogid;
    }


    /**
     * Sensor relational Objects
     */


    /**
     * @return Sensor
     */
    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $sensorNameID
     */
    public function setSensorNameID(Sensor $sensorNameID): void
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
    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param int|float $reading
     */
    public function setCurrentReading(int|float $reading): void
    {
        $this->analogReading = $reading;
    }

    /**
     * @param int|float|string $reading
     */
    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highAnalog = $reading;
        }
    }

    /**
     * @param int|float|string $reading
     */
    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowAnalog = $reading;
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
