<?php

namespace App\ESPDeviceSensor\Entity;

use App\Devices\Entity\Devices;
use App\Form\CustomFormValidators as NoSpecialCharacters;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sensors.
 *
 * @ORM\Table(name="sensornames", indexes={@ORM\Index(name="sensornames_ibfk_2", columns={"createdBy"}), @ORM\Index(name="SensorType", columns={"sensorTypeID"}), @ORM\Index(name="sensornames_ibfk_1", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorsRepository")
 */
class Sensor
{
    public const TEMPERATURE = 'Temperature';

    public const HUMIDITY = 'Humidity';

    public const ANALOG = 'Analog';

    public const LATITUDE   = 'Latitude';

    public const ALIAS  = 'sensors';

    private const SENSOR_NAME_MAX_LENGTH = 20;

    private const SENSOR_NAME_MIN_LENGTH = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="sensorNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorNameID;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorName", type="string", length=20, nullable=false)
     */
    #[
        NoSpecialCharacters\NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::SENSOR_NAME_MIN_LENGTH,
            max: self::SENSOR_NAME_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $sensorName;

    /**
     * @var SensorType
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\SensorType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorTypeID", referencedColumnName="sensorTypeID")
     * })
     */
    private SensorType $sensorTypeID;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Devices\Entity\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private Devices $deviceNameID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createdBy", referencedColumnName="userID")
     * })
     */
    private User $createdBy;

    /**
     * @return int
     */
    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    /**
     * @param int $sensorNameID
     */
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

    /**
     * @return Devices
     */
    public function getDeviceObject(): Devices
    {
        return $this->deviceNameID;
    }

    /**
     * @param Devices $deviceNameID
     */
    public function setDeviceObject(Devices $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }


}
