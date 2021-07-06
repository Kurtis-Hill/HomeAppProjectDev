<?php

namespace App\Entity\Sensors\ReadingTypes;


use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Mapping\ClassMetadata;


/**
 * Temp
 *
 * @ORM\Table(name="temp", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="temp_ibfk_6", columns={"deviceNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="GroupName", columns={"groupNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\TempRepository")
 */
class Temperature implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
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
    private float $currentReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="26"})
     */
    private float $highTemp = 50;

    /**
     * @var float
     *
     * @ORM\Column(name="lowTemp", type="float", precision=10, scale=0, nullable=false, options={"default"="12"})
     */
    private float $lowTemp = 10;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private ?\DateTime $time;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
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

    /**
     * @return int|float
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
     * @return \DateTime
     */
    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param int|float $reading
     */
    public function setCurrentSensorReading(int|float $reading): void
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
     * @param \DateTime|null $time
     */
    public function setTime(?\DateTime $time = null): void
    {
        $this->time = $time ?? new \DateTime('now');
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

    #[Pure] public function getMeasurementDifferenceHighReading(): int|float
    {
        return $this->getHighReading() - $this->getCurrentReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentReading();
    }

    public function isReadingOutOfBounds(): bool
    {
        if ($this->getCurrentReading() <= $this->getHighReading()) {
            return true;
        }
        if ($this->getCurrentReading() <= $this->getLowReading()) {
            return true;
        }

        return false;
    }

    public function getSensorTypeName(): string
    {
        return 'temperature';
    }


}
