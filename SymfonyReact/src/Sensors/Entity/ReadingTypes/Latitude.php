<?php

namespace App\Sensors\Entity\ReadingTypes;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\ReadingType\LatitudeRepository")
 */
class Latitude extends AbstractReadingType implements AllSensorReadingTypeInterface, StandardReadingSensorInterface
{
    public const READING_TYPE = 'latitude';

    public const HIGH_READING = 90;

    public const LOW_READING = -90;

    public const READING_SYMBOL = '°';

    public const LATITUDE_SENSORS = [
        Bmp::NAME
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
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensor $sensorNameID;

    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'Latitude date time should not be blank')]
    private DateTimeInterface $updatedAt;

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
        return $this->updatedAt;
    }

    public function setCurrentReading(int|float|string $currentReading): void
    {
        $this->latitude = $currentReading;
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
        $this->updatedAt = new DateTimeImmutable('now');
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
}
