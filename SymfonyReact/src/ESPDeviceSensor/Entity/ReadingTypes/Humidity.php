<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Humidity
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ReadingType\HumidityRepository")
 */
//#[Assert\Callback([SensorReadingTypesValidatorServiceInterface::class, 'validate'])]
class Humidity extends AbstractReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'humidity';

    public const READING_SYMBOL = '%';

    public const HIGH_READING = 100;

    public const LOW_READING = 0;


    /**
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $humidID;

    /**
     * @ORM\Column(name="humidReading", type="integer", precision=10, scale=0, nullable=false)
     */
    #[HumidityConstraint]
    private int $currentReading;

    /**
     * @ORM\Column(name="highHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    #[HumidityConstraint]
    private int $highHumid = 80;

    /**
     * @ORM\Column(name="lowHumid", type="integer", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    #[HumidityConstraint]
    private int $lowHumid = 10;

    /**
     * @ORM\Column(name="constRecord", type="boolean", nullable=false, options={"default"="0"})
     */
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'humidity date time should not be blank')]
    private DateTimeInterface $updateAt;

    /**
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

    public function setSensorObject(Sensor $id): void
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

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updateAt;
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

    public function setUpdatedAt(): void
    {
        $this->updateAt = new DateTimeImmutable('now');
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

    public function getSensorReadingTypeObjectString(): string
    {
        return self::class;
    }
}
