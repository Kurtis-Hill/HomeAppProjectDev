<?php

namespace App\Entity\Sensor\ReadingTypes\StandardReadingTypes;

use App\Entity\Sensor\ReadingTypes\BaseReadingTypeInterface;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[
    Entity(repositoryClass: StandardReadingTypeRepository::class),
    ORM\Table(name: "standardreadingtype"),
    ORM\Index(columns: ["currentReading"], name: "currentReading"),
    ORM\Index(columns: ["highReading"], name: "highReading"),
    ORM\Index(columns: ["lowReading"], name: "lowReading"),
    ORM\Index(columns: ["constRecord"], name: "constRecord"),
    ORM\Index(columns: ["updatedAt"], name: "updatedAt"),
    ORM\Index(columns: ["standardReadingType"], name: "standardreadingtypeIndex"),
    ORM\Index(columns: ["sensorID"], name: "sensorID"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    InheritanceType('SINGLE_TABLE'),
    DiscriminatorColumn(name: 'standardReadingType', type: 'string'),
    DiscriminatorMap(
        [
            Temperature::READING_TYPE => Temperature::class,
            Humidity::READING_TYPE => Humidity::class,
            Analog::READING_TYPE => Analog::class,
            Latitude::READING_TYPE => Latitude::class,
        ]
    )
]
abstract class AbstractStandardReadingType implements BaseReadingTypeInterface, StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    protected const HIGHER_LOWER_THAN_LOWER = 'High reading for %s cannot be lower than low reading';

    #[
        ORM\Column(name: 'readingTypeID', type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $readingTypeID;

    #[
        ORM\OneToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseReadingTypeID", referencedColumnName: "baseReadingTypeID"),
    ]
    private BaseSensorReadingType $baseReadingType;

    #[ORM\Column(name: 'currentReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $currentReading;

    #[ORM\Column(name: 'highReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $highReading = 0;

    #[ORM\Column(name: 'lowReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $lowReading = 0;

    public function getBaseReadingType(): BaseSensorReadingType
    {
        return $this->baseReadingType;
    }

    public function setBaseReadingType(BaseSensorReadingType $readingType): void
    {
        $this->baseReadingType = $readingType;
    }

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function setReadingTypeID(): string
    {
        return $this->readingTypeID;
    }

    public function getCurrentReading(): int|float
    {
        return $this->currentReading;
    }

    public function setCurrentReading(int|float|string $currentReading): void
    {
        $this->currentReading = $currentReading;
    }

    public function getHighReading(): int|float
    {
        return $this->highReading;
    }

    public function setHighReading(int|float $highReading): void
    {
        $this->highReading = $highReading;
    }

    public function getLowReading(): int|float
    {
        return $this->lowReading;
    }

    public function setLowReading(int|float $lowReading): void
    {
        $this->lowReading = $lowReading;
    }

    public function isReadingOutOfBounds(): bool
    {
        return $this->getCurrentReading() > $this->getHighReading()
            || $this->getCurrentReading() < $this->getLowReading();
    }

    public function getMeasurementDifferenceHighReading(): int|float
    {
        return $this->getHighReading() - $this->getCurrentReading();
    }

    public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentReading();
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getHighReading() < $this->getLowReading()) {
            $context
                ->buildViolation(sprintf(self::HIGHER_LOWER_THAN_LOWER, $this->getReadingType()))
                ->addViolation();
        }
    }

    public function getSensor(): Sensor
    {
        return $this->getBaseReadingType()->getSensor();
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->getBaseReadingType()->setSensor($sensor);
    }

    public function getConstRecord(): bool
    {
        return $this->getBaseReadingType()->getConstRecord();
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->getBaseReadingType()->setConstRecord($constRecord);
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->getBaseReadingType()->getUpdatedAt();
    }

    public function setUpdatedAt(): void
    {
        $this->getBaseReadingType()->setUpdatedAt();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->getBaseReadingType()->getCreatedAt();
    }

    public function setCreatedAt(?DateTimeInterface $createdAt = null): void
    {
        if ($createdAt === null) {
            $createdAt = new DateTimeImmutable('now');
        }
        $this->getBaseReadingType()->setCreatedAt($createdAt);
    }
}
