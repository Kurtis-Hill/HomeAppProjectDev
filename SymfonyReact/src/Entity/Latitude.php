<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Latitude
{
    /**
     * @var int
     *
     * @ORM\Column(name="latitudeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $latitudeid;

    /**
     * @var int
     *
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    private $latitude;

    /**
     * @var int
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private $lowlatitude;

    /**
     * @var int
     *
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    private $highlatitude;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private $constrecord = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $updatedat = 'current_timestamp()';

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
