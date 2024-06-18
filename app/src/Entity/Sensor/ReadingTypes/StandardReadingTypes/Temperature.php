<?php

namespace App\Entity\Sensor\ReadingTypes\StandardReadingTypes;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Interfaces\ReadingSymbolInterface;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Repository\Sensor\ReadingType\ORM\TemperatureRepository;
use App\Services\CustomValidators\Sensor\SensorDataValidators\BMP280TemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\SHTTemperatureConstraint;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[
    ORM\Entity(repositoryClass: TemperatureRepository::class),
]
class Temperature extends AbstractStandardReadingType implements ReadingSymbolInterface
{
    public const READING_TYPE = 'temperature';

    public const READING_SYMBOL = '°C';

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
    protected float $currentReading;

//    #[ORM\Column(name: 'highTemp', type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "26"])]
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
    protected float $highReading = 50;

//    #[ORM\Column(name: 'lowTemp', type: "float", precision: 10, scale: 0, nullable: false, options: ["default" => "12"]),]
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
    protected float $lowReading = 10;

//    #[ORM\Column(name: 'constRecord', type: "boolean", nullable: false, options: ["default" => "0"])]
//    #[Assert\Type("bool")]
//    private bool $constRecord = false;

//    #[ORM\Column(name: 'updatedAt', type: "datetime", nullable: false, options: ["default" => "current_timestamp()"])]
//    #[Assert\NotBlank(message: 'temperature date time name should not be blank')]
//    private DateTimeInterface $updatedAt;
//
//    #[
//        ORM\ManyToOne(targetEntity: Sensor::class),
//        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
//    ]
//    private Sensor $sensor;

    public function getSensorID(): int
    {
        return $this->getReadingTypeID();
    }

//    public function getSensor(): Sensor
//    {
//        return $this->sensor;
//    }

//    public function setSensor(Sensor $id): void
//    {
//        $this->sensor = $id;
//    }

//    /**
//     * Sensor Reading Methods
//     */
//    #[Pure]
//    public function getCurrentReading(): int|float
//    {
//        return $this->currentReading;
//    }
//
//    public function getHighReading(): int|float
//    {
//        return $this->highReading;
//    }
//
//    public function getLowReading(): int|float
//    {
//        return $this->lowReading;
//    }
//
//    public function setCurrentReading(int|float|string|bool $reading): void
//    {
//        $this->currentReading = $reading;
//    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highReading = (float)$reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowReading = $reading;
        }
    }

//    public function setUpdatedAt(): void
//    {
//        $this->updatedAt = new DateTimeImmutable('now');
//    }

//    public function getConstRecord(): bool
//    {
//        return $this->constRecord;
//    }

//    public function setConstRecord(bool $constRecord): void
//    {
//        $this->constRecord = $constRecord;
//    }

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
