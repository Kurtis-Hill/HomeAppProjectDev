<?php

namespace App\Sensors\Entity\ReadingTypes\StandardReadingTypes;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SHTTemperatureConstraint;
use App\Sensors\Repository\ReadingType\ORM\TemperatureRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[
    ORM\Entity(repositoryClass: TemperatureRepository::class),
    ORM\Table(name: "temperature"),
    ORM\UniqueConstraint(name: "temp_ibfk_1", columns: ["sensorID"]),
]
class Temperature extends AbstractStandardReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface, ReadingSymbolInterface
{
    public const READING_TYPE = 'temperature';

    public const READING_SYMBOL = '°C';

    #[
        ORM\Column(name: 'tempID', type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $tempID;

    #[ORM\Column(name: 'tempReading', type: "float", precision: 10, scale: 0, nullable: false)]
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups:[Sht::NAME]
        ),
    ]
    private float $currentReading;

    #[ORM\Column(name: 'highTemp', type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "26"])]
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups:[Sht::NAME]
        ),
        Assert\Callback([self::class, 'validate'])
    ]
    private float $highTemp = 50;

    #[ORM\Column(name: 'lowTemp', type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "12"]),]
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups:[Sht::NAME]
        ),
    ]
    private float $lowTemp = 10;

    #[ORM\Column(name: 'constRecord', type: "boolean", nullable: false, options: ["default" => "0"])]
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    #[ORM\Column(name: 'updatedAt', type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
    #[Assert\NotBlank(message: 'temperature date time name should not be blank')]
    private DateTimeInterface $updatedAt;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    public function getSensorID(): int
    {
        return $this->tempID;
    }

    public function setSensorID(int $id): void
    {
        $this->tempID = $id;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $id): void
    {
        $this->sensor = $id;
    }

    /**
     * Sensor Reading Methods
     */
    #[Pure]
    public function getCurrentReading(): int|float
    {
        return $this->currentReading;
    }

    public function getHighReading(): int|float
    {
        return $this->highTemp;
    }

    public function getLowReading(): int|float
    {
        return $this->lowTemp;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setCurrentReading(int|float|string|bool $reading): void
    {
        $this->currentReading = $reading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highTemp = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowTemp = $reading;
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

    public static function getReadingSymbol(): string
    {
        return self::READING_SYMBOL;
    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    #[Assert\Callback(groups: [Dht::NAME, Dallas::NAME, Bmp::NAME, Sht::NAME])]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getHighReading() < $this->getLowReading()) {
            $context
                ->buildViolation(sprintf(self::HIGHER_LOWER_THAN_LOWER, $this->getReadingType()))
                ->addViolation();
        }
    }
}
