<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardstate
 *
 * @ORM\Table(name="cardstate", uniqueConstraints={@ORM\UniqueConstraint(name="state", columns={"state"})})
 * @ORM\Entity
 */
class Cardstate
{
    /**
     * @var int
     *
     * @ORM\Column(name="cardStateID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cardstateid;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=false)
     */
    private $state;


}
