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
