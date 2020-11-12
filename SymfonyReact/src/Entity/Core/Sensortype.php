<?php

namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensortype
 *
 * @ORM\Table(name="sensortype")
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

    public function getSensortypeid(): ?int
    {
        return $this->sensortypeid;
    }

    public function getSensortype(): ?string
    {
        return $this->sensortype;
    }

    public function setSensortype(string $sensortype): self
    {
        $this->sensortype = $sensortype;

        return $this;
    }


}
