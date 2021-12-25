<?php

namespace App\Entity\New;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bmp
 *
 * @ORM\Table(name="bmp", uniqueConstraints={@ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="latitudeID", columns={"latitudeID"}), @ORM\UniqueConstraint(name="tempID*", columns={"tempID"})}, indexes={@ORM\Index(name="bmp_ibfk_1", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Bmp
{
    /**
     * @var int
     *
     * @ORM\Column(name="bmpID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bmpid;

    /**
     * @var \Sensornames
     *
     * @ORM\ManyToOne(targetEntity="Sensornames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    /**
     * @var \Temp
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private $tempid;

    /**
     * @var \Humid
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private $humidid;

    /**
     * @var \Latitude
     *
     * @ORM\ManyToOne(targetEntity="Latitude")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="latitudeID", referencedColumnName="latitudeID")
     * })
     */
    private $latitudeid;


}
