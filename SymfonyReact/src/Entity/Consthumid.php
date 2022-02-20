<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consthumid
 *
 * @ORM\Table(name="consthumid", indexes={@ORM\Index(name="sensorID", columns={"humidID"})})
 * @ORM\Entity
 */
class Consthumid
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
