<?php

namespace App\Sensors\Entity;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\Devices\Entity\Devices;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Sensors\Repository\ORM\Sensors\SensorRepository;

#[
    ORM\Entity(repositoryClass: SensorRepository::class),
    ORM\Table(name: "sensornames"),
    ORM\UniqueConstraint(name: "sensornames_ibfk_2", columns: ["createdBy"]),
    ORM\UniqueConstraint(name: "SensorType", columns: ["sensorTypeID"]),
    ORM\UniqueConstraint(name: "sensornames_ibfk_1", columns: ["deviceNameID"]),
    ORM\Index(columns: ["deviceNameID"], name: "sensornames_ibfk_3"),
    ORM\Index(columns: ["sensorTypeID"], name: "sensornames_ibfk_4"),
    ORM\Index(columns: ["createdBy"], name: "sensornames_ibfk_5"),
]
class Sensor
{
    public const ALIAS  = 'sensors';

    private const SENSOR_NAME_MAX_LENGTH = 20;

    private const SENSOR_NAME_MIN_LENGTH = 2;

    #[
        ORM\Column(name: "sensorNameID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $sensorNameID;

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
        ORM\JoinColumn(name: "deviceNameID", referencedColumnName: "deviceNameID"),
    ]
    private Devices $deviceNameID;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "createdBy", referencedColumnName: "userID"),
    ]
    private User $createdBy;

    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    public function setSensorNameID(int $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
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

    public function getDeviceObject(): Devices
    {
        return $this->deviceNameID;
    }

    public function setDeviceObject(Devices $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
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
