<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Outofrangehumid
 *
 * @ORM\Table(name="outofrangehumid", indexes={@ORM\Index(name="sensorID", columns={"humidID"})})
 * @ORM\Entity
 */
class Outofrangehumid
{
    /**
     * @var int
     *
     * @ORM\Column(name="outofrangeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $outofrangeid;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $sensorreading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $createdat = 'current_timestamp()';

    /**
     * @var \Humid
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private $humidid;


}
