<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Icons
 *
 * @ORM\Table(name="icons", uniqueConstraints={@ORM\UniqueConstraint(name="iconName_2", columns={"iconName"})})
 * @ORM\Entity
 */
class Icons
{
    /**
     * @var int
     *
     * @ORM\Column(name="iconID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iconid;

    /**
     * @var string
     *
     * @ORM\Column(name="iconName", type="string", length=20, nullable=false)
     */
    private $iconname;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=20, nullable=false)
     */
    private $description;


}
