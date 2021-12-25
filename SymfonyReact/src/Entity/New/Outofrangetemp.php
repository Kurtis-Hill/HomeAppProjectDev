<?php

namespace App\Entity\New;

use Doctrine\ORM\Mapping as ORM;

/**
 * Outofrangetemp
 *
 * @ORM\Table(name="outofrangetemp", indexes={@ORM\Index(name="outofrangetemp_ibfk_1", columns={"tempID"})})
 * @ORM\Entity
 */
class Outofrangetemp
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
     * @var \Temp
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private $tempid;


}
