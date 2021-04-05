<?php

namespace App\Entity\Sensors;

use Doctrine\ORM\Mapping as ORM;

/**
 * SensorTypes
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorTypeRepository")
 */
class SensorType
{
    public const OUT_OF_BOUND_FORM_ARRAY_KEY = 'outOfBounds';

    public const UPDATE_CURRENT_READING_FORM_ARRAY_KEY = 'updateCurrentReading';

    public const DHT_SENSOR = 'DHT';

    public const BMP_SENSOR = 'BMP';

    public const DALLAS_TEMPERATURE = 'Dallas Temperature';

    public const SOIL_SENSOR = 'Soil';

    /**
     * @var int
     *
     * @ORM\Column(name="sensorTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorTypeID;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorType", type="string", length=20, nullable=false)
     */
    private string $sensorType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    private string $description;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    /**
     * @param int $sensorTypeID
     */
    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @param string $sensorType
     */
    public function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}
