<?php

namespace App\Sensors\Entity;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\Devices\Entity\Devices;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: SensorRepository::class),
    ORM\Table(name: "sensors"),
    ORM\UniqueConstraint(name: "sensor_device", columns: ["sensorName", "deviceID"]),
    ORM\Index(columns: ["ddeviceID"], name: "sensornames_ibfk_1"),
    ORM\Index(columns: ["createdBy"], name: "sensornames_ibfk_2"),
    ORM\Index(columns: ["sensorTypeID"], name: "sensortype"),
]
class Sensor
{
    public const ALIAS  = 'sensors';

    private const SENSOR_NAME_MAX_LENGTH = 50;

    private const SENSOR_NAME_MIN_LENGTH = 2;

    #[
        ORM\Column(name: "sensorID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $sensorID;

    #[ORM\Column(name: "sensorName", type: "string", length: 20, nullable: false)]
    #[
        NoSpecialCharactersConstraint,
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
        ORM\ManyToOne(targetEntity: SensorType::class),
        ORM\JoinColumn(name: "sensorTypeID", referencedColumnName: "sensorTypeID"),
    ]
    private SensorType $sensorTypeID;

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

    public function getSensorTypeObject(): SensorType
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(SensorType $sensorTypeID): void
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
}
