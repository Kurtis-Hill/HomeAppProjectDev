<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Outofrangeanalog
 *
 * @ORM\Table(name="outofrangeanalog", indexes={@ORM\Index(name="sensorID", columns={"analogID"})})
 * @ORM\Entity
 */
class Outofrangeanalog
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
     * @var float|null
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=true, options={"default"="NULL"})
     */
    private $sensorreading = NULL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private $createdat = 'current_timestamp()';

    /**
     * @var \Analog
     *
     * @ORM\ManyToOne(targetEntity="Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private $analogid;


}
