<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensortype
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity
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

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    private $description;


}
