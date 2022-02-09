<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\LatitudeRepository")
 */
class Latitude extends AbstractReadingType implements AllSensorReadingTypeInterface, StandardReadingSensorInterface
{
    public const READING_TYPE = 'latitude';

    public const HIGH_READING = 90;

    public const LOW_READING = -90;

    public const LATITUDE_SENSORS = [
        SensorType::BMP_SENSOR
    ];

    /**
     * @ORM\Column(name="latitudeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $latitudeID;

    /**
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    #[LatitudeConstraint]
    private int|float $latitude;

    /**
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    #[LatitudeConstraint]
    private int|float $highLatitude = 90;

    /**
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    #[LatitudeConstraint]
    private int|float $lowLatitude = -90;

    /**
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    #[Assert\Type("bool")]
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
    #[Assert\NotBlank(message: 'Latitude date time should not be blank')]
    private DateTimeInterface $time;

    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    public function setSensorID(int $latitudeId): void
    {
        $this->latitudeID = $latitudeId;
    }

    /**
     * Sensor relational Objects
     */

    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorObject(Sensor $id): void
    {
        $this->sensorNameID = $id;
    }

    public function getCurrentReading(): int
    {
        return $this->latitude;
    }

    public function getHighReading(): int
    {
        return $this->highLatitude;
    }

    public function getLowReading(): int
    {
        return $this->lowLatitude;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->time;
    }

    public function setCurrentReading(int|float $reading): void
    {
        $this->latitude = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highLatitude = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowLatitude = $reading;
        }
    }

    public function setUpdatedAt(): void
    {
        $this->time = new DateTimeImmutable('now');
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public function getSensorReadingTypeObjectString(): string
    {
        return self::class;
    }
}
