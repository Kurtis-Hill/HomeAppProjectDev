<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Constanalog
 *
 * @ORM\Table(name="constanalog", indexes={@ORM\Index(name="sensorID", columns={"analogID"})})
 * @ORM\Entity
 */
class Constanalog
{
    /**
     * @var int
     *
     * @ORM\Column(name="constRecordID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $constrecordid;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $sensorreading;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=false)
     */
    private $createdat;

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
