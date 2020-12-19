<?php

namespace App\Entity\Sensors;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensortype
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorTypeRepository")
 */
class Sensortype
{
    public const DHT_SENSOR = 'DHT';

    public const BMP_SENSOR = 'BPM Weather Station';

    public const DALLAS_TEMPERATURE = 'Dallas Temperature';

    public const SOIL_SENSOR = 'Soil';

    /**
     * @var int
     *
     * @ORM\Column(name="sensorTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sensortypeid;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorType", type="string", length=20, nullable=false)
     */
    private $sensortype;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    private $description;

    /**
     * @return int
     */
    public function getSensortypeid(): int
    {
        return $this->sensortypeid;
    }

    /**
     * @param int $sensortypeid
     */
    public function setSensortypeid(int $sensortypeid): void
    {
        $this->sensortypeid = $sensortypeid;
    }

    /**
     * @return string
     */
    public function getSensortype(): string
    {
        return $this->sensortype;
    }

    /**
     * @param string $sensortype
     */
    public function setSensortype(string $sensortype): void
    {
        $this->sensortype = $sensortype;
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
