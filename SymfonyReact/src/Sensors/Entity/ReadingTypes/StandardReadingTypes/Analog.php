<?php

namespace App\Sensors\Entity\ReadingTypes\StandardReadingTypes;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
use App\Sensors\Repository\ReadingType\ORM\AnalogRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[
    ORM\Entity(repositoryClass: AnalogRepository::class),
    ORM\Table(name: "analog"),
//    ORM\UniqueConstraint(name: "analog_ibfk_3", columns: ["sensorID"]),
]
class Analog extends AbstractStandardReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'analog';

//    #[
//        ORM\Column(name: "analogID", type: "integer", nullable: false),
//        ORM\Id,
//        ORM\GeneratedValue(strategy: "NONE"),
//    ]
//    private int $analogID;

//    #[ORM\Column(name: "analogReading", type: "float", precision: 10, scale: 0, nullable: true, options: ["default" => "NULL"])]
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME])
    ]
    protected float $currentReading;

//    #[ORM\Column(name: "highAnalog", type: "float", precision: 10, scale: 0, nullable: true, options: ["default" => "1000"])]
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME])
    ]
    protected float $highReading;

//    #[ORM\Column(name: "lowAnalog", type: "float", precision: 10, scale: 0, nullable: true, options: ["default" => "1000"])]
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME])
    ]
    protected float $lowReading;

//    #[ORM\Column(name: "constRecord", type: "boolean", nullable: true)]
//    #[Assert\Type("bool")]
//    private bool $constRecord = false;

//    #[ORM\Column(name: "updatedAt", type: "datetime", precision: 0, nullable: false, options: ["default" => "current_timestamp()"])]
//    #[Assert\NotBlank(message: 'analog date time should not be blank')]
//    private DateTimeInterface $updatedAt;

//    #[
//        ORM\ManyToOne(targetEntity: Sensor::class),
//        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
//    ]
//    private Sensor $sensor;

    public function getSensorID(): int
    {
        return $this->readingTypeID;
    }

    public function setSensorID(int $analogid): void
    {
        $this->readingTypeID = $analogid;
    }

//    public function getSensor(): Sensor
//    {
//        return $this->sensor;
//    }
//
//    /**
//     * @param Sensor $sensor
//     */
//    public function setSensor(Sensor $sensor): void
//    {
//        $this->sensor = $sensor;
//    }
//
//    /**
//     * Sensor Reading Methods
//     */
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
//    public function getUpdatedAt(): DateTimeInterface
//    {
//        return $this->updatedAt;
//    }
//
//    public function setCurrentReading(int|float|string|bool $reading): void
//    {
//        $this->currentReading = $reading;
//    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highReading = $reading;
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
//
//    public function getConstRecord(): bool
//    {
//        return $this->constRecord;
//    }
//
//    public function setConstRecord(bool $constRecord): void
//    {
//        $this->constRecord = $constRecord;
//    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    public static function getReadingTypeName(): string
    {
        return self::READING_TYPE;
    }

    #[Assert\Callback(groups: [Soil::NAME])]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getHighReading() < $this->getLowReading()) {
            $context
                ->buildViolation('High reading for ' . $this->getReadingType() . ' cannot be lower than low reading')
                ->addViolation();
        }
    }
}
