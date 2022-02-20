<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Humid
 *
 * @ORM\Table(name="humid", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Humid
{
    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $humidid;

    /**
     * @var float
     *
     * @ORM\Column(name="humidReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $humidreading;

    /**
     * @var float
     *
     * @ORM\Column(name="highHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private $highhumid = 70;

    /**
     * @var float
     *
     * @ORM\Column(name="lowHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private $lowhumid = 15;

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
