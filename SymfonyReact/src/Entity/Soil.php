<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="cardViewID", columns={"sensorNameID"}), @ORM\UniqueConstraint(name="analogID", columns={"analogID"})})
 * @ORM\Entity
 */
class Soil
{
    /**
     * @var int
     *
     * @ORM\Column(name="soilID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $soilid;

    /**
     * @var \Analog
     *
     * @ORM\ManyToOne(targetEntity="Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private $analogid;

    /**
     * @var \Sensornames
     *
     * @ORM\ManyToOne(targetEntity="Sensornames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;


}
