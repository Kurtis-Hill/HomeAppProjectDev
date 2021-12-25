<?php

namespace App\Entity\New;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardcolour
 *
 * @ORM\Table(name="cardcolour", uniqueConstraints={@ORM\UniqueConstraint(name="colour", columns={"colour"}), @ORM\UniqueConstraint(name="shade", columns={"shade"})})
 * @ORM\Entity
 */
class Cardcolour
{
    /**
     * @var int
     *
     * @ORM\Column(name="colourID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $colourid;

    /**
     * @var string
     *
     * @ORM\Column(name="colour", type="string", length=20, nullable=false)
     */
    private $colour;

    /**
     * @var string
     *
     * @ORM\Column(name="shade", type="string", length=20, nullable=false)
     */
    private $shade;


}
