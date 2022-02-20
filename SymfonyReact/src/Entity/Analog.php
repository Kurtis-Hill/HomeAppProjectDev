<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Analog
 *
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="analog_ibfk_3", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Analog
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $analogid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="analogReading", type="smallint", nullable=true, options={"default"="NULL"})
     */
    private $analogreading = 'NULL';

    /**
     * @var int|null
     *
     * @ORM\Column(name="highAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    private $highanalog = '1000';

    /**
     * @var int|null
     *
     * @ORM\Column(name="lowAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    private $lowanalog = '1000';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true)
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
