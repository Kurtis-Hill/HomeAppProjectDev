<?php

namespace App\Entity\Sensor;

use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Services\CustomValidators\NoSpecialCharactersNameConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: SensorRepository::class),
    ORM\Table(name: "sensors"),
    ORM\UniqueConstraint(name: "sensor_device", columns: ["sensorName", "deviceID"]),
    ORM\Index(columns: ["deviceID"], name: "sensornames_ibfk_1"),
    ORM\Index(columns: ["createdBy"], name: "sensornames_ibfk_2"),
    ORM\Index(columns: ["sensorTypeID"], name: "sensortype"),
    ORM\Index(columns: ["sensorName"], name: "sensorName")
]
class Sensor
{
    public const DEFAULT_READING_INTERVAL = 6000;

    public const ALIAS  = 'sensors';

    public const SENSOR_NAME_MAX_LENGTH = 20;

    public const SENSOR_NAME_MIN_LENGTH = 2;

    public const MIN_READING_INTERVAL = 500;

    #[
        ORM\Column(name: "sensorID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $sensorID;

    #[ORM\Column(name: "sensorName", type: "string", length: 20, nullable: false)]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::SENSOR_NAME_MIN_LENGTH,
            max: self::SENSOR_NAME_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $sensorName;

    #[
        ORM\ManyToOne(targetEntity: AbstractSensorType::class),
        ORM\JoinColumn(name: "sensorTypeID", referencedColumnName: "sensorTypeID"),
    ]
    private AbstractSensorType $sensorTypeID;

    #[
        ORM\ManyToOne(targetEntity: Devices::class),
        ORM\JoinColumn(name: "deviceID", referencedColumnName: "deviceID"),
    ]
    private Devices $deviceID;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "createdBy", referencedColumnName: "userID"),
    ]
    private User $createdBy;

    #[ORM\Column(name: "pinNumber", type: "smallint", nullable: false)]
    #[
        Assert\Range(
            notInRangeMessage: 'pinNumber must be greater than {{ min }}',
            minMessage: 'pinNumber must be greater than {{ value }}',
            invalidMessage: 'pinNumber must be an int you have provided {{ value }}',
            min: 0,
        ),
    ]
    private int $pinNumber;

    #[
        ORM\Column(name: "takeReadingIntervalMilli", type: "integer", nullable: false),
        Assert\Range(
//            notInRangeMessage: "readingInterval must be between {{ min }} and {{ max }}",
            notInRangeMessage: "readingInterval must be greater than {{ min }}",
            minMessage: "readingInterval must be greater than " . self::MIN_READING_INTERVAL,
            invalidMessage: "readingInterval must be a number",
            min: self::MIN_READING_INTERVAL,
//            max: 100000
        ),
    ]
    private int $readingInterval = self::DEFAULT_READING_INTERVAL;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false),
    ]
    private DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    public function setSensorID(int $sensorID): void
    {
        $this->sensorID = $sensorID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function getSensorTypeObject(): AbstractSensorType
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(AbstractSensorType $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getDevice(): Devices
    {
        return $this->deviceID;
    }

    public function setDevice(Devices $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getPinNumber(): ?int
    {
        return $this->pinNumber;
    }

    public function setPinNumber(?int $pinNumber): void
    {
        $this->pinNumber = $pinNumber;
    }

    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }

    public function setReadingInterval(int $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        if ($createdAt === null) {
            $createdAt = new DateTimeImmutable('now');
        }
        $this->createdAt = $createdAt;
    }
}
