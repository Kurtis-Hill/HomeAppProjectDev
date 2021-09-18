<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"}), @ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\LatitudeRepository")
 */
class Latitude implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
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
    private int|float $highLatitude = 66.932045;

    /**
     * @var int|float
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private int|float $lowLatitude = 58.008098;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    private bool $constRecord = false;

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
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private ?\DateTime $time;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->latitudeID = $analogid;
    }

    /**
     * Sensor relational Objects
     */

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
     * @return float|int
     */
    public function getCurrentReading(): int|float
    {
        return $this->latitude;
    }

    /**
     * @return float|int
     */
    public function getHighReading(): int|float
    {
        return $this->highLatitude;
    }

    /**
     * @return float|int
     */
    public function getLowReading(): int|float
    {
        return $this->lowLatitude;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTimeInterface
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
        return $this->getCurrentReading() - $this->getHighReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getCurrentReading() - $this->getLowReading();
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
        return 'latitude';
    }
}
