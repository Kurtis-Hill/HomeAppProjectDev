<?php

namespace App\Sensors\Entity\ReadingTypes\StandardReadingTypes;

use App\Sensors\Entity\ReadingTypes\AbstractSensorReadingType;
use App\Sensors\Entity\Sensor;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
#[ORM\Table(name: "standardReadingType")]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'readingType', type: 'string')]
#[DiscriminatorMap(
    [
        Temperature::READING_TYPE => Temperature::class,
        Humidity::READING_TYPE => Humidity::class,
        Analog::READING_TYPE => Analog::class,
        Latitude::READING_TYPE => Latitude::class,
    ]
)]
abstract class AbstractStandardReadingType extends AbstractSensorReadingType
{
    protected const HIGHER_LOWER_THAN_LOWER = 'High reading for %s cannot be lower than low reading';

//    #[
//        ORM\Column(name: 'readingTypeID', type: "integer", nullable: false),
//        ORM\Id,
//        ORM\GeneratedValue(strategy: "IDENTITY"),
//    ]
//    protected int $readingTypeID;

    #[ORM\Column(name: 'readingType', type: "string", nullable: false)]
    protected string $readingType;

//    #[
//        ORM\ManyToOne(targetEntity: Sensor::class),
//        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
//    ]
//    private Sensor $sensor;

    #[ORM\Column(name: 'currentReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $currentReading;

    #[ORM\Column(name: 'highReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $highReading;

    #[ORM\Column(name: 'lowReading', type: "float", precision: 10, scale: 0, nullable: false)]
    protected float $lowReading;

    #[ORM\Column(name: 'constRecord', type: "boolean", nullable: false, options: ["default" => "0"])]
    #[Assert\Type("bool")]
    protected bool $constRecord = false;

//    #[ORM\Column(name: 'updatedAt', type: "datetime", nullable: false,
////        options: ["default" => "current_timestamp()"]
//)
//    ]
//
//    #[Assert\NotBlank(message: 'date time name should not be blank')]
//    protected DateTimeInterface $updatedAt;
//
//    #[ORM\Column(name: 'createdAt', type: "datetime", nullable: false,
////        options: ["default" => "current_timestamp()"]
//    )]
//    #[Assert\NotBlank(message: 'createdAt time name should not be blank')]
//    protected DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable('now'));
    }

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function setReadingTypeID(): string
    {
        return $this->readingTypeID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
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

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isReadingOutOfBounds(): bool
    {
        return $this->getCurrentReading() >= $this->getHighReading()
            || $this->getCurrentReading() <= $this->getLowReading();
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
}
