<?php

namespace App\Sensors\Entity;

use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GetSensorTypesController
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\Sensors\SensorTypeRepository")
 */
class SensorType
{
    public const ALIAS = 'sensortype';

    public const ALL_SENSOR_TYPES = [
        Bmp::NAME,
        Soil::NAME,
        Dallas::NAME,
        Dht::NAME,
    ];

    private const SENSOR_TYPE_DESCRIPTION_MIN_LENGTH = 5;

    private const SENSOR_TYPE_DESCRIPTION_MAX_LENGTH = 50;

    /**
     * @ORM\Column(name="sensorTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorTypeID;

    /**
     * @ORM\Column(name="sensorType", type="string", length=20, nullable=false)
     */
    #[NoSpecialCharactersConstraint]
    private string $sensorType;

    /**
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    #[
        NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::SENSOR_TYPE_DESCRIPTION_MIN_LENGTH,
            max: self::SENSOR_TYPE_DESCRIPTION_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $description;

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
