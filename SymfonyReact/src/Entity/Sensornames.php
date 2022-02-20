<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensornames
 *
 * @ORM\Table(name="sensornames", indexes={@ORM\Index(name="sensornames_ibfk_1", columns={"deviceNameID"}), @ORM\Index(name="sensornames_ibfk_2", columns={"createdBy"}), @ORM\Index(name="SensorType", columns={"sensorTypeID"})})
 * @ORM\Entity
 */
class Sensornames
{
    /**
     * @var int
     *
     * @ORM\Column(name="sensorNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sensornameid;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorName", type="string", length=20, nullable=false)
     */
    private $sensorname;

    /**
     * @var \Sensortype
     *
     * @ORM\ManyToOne(targetEntity="Sensortype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorTypeID", referencedColumnName="sensorTypeID")
     * })
     */
    private $sensortypeid;

    /**
     * @var \Devicenames
     *
     * @ORM\ManyToOne(targetEntity="Devicenames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private $devicenameid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createdBy", referencedColumnName="userID")
     * })
     */
    private $createdby;


}
