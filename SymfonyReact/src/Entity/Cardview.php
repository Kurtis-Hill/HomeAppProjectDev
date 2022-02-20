<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", indexes={@ORM\Index(name="FK_E36636B5840D9A7A", columns={"cardIconID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"}), @ORM\Index(name="FK_E36636B5A356FF88", columns={"cardColourID"}), @ORM\Index(name="FK_E36636B53BE475E6", columns={"sensorNameID"}), @ORM\Index(name="FK_E36636B53casrdState", columns={"cardStateID"}), @ORM\Index(name="UserID", columns={"userID"})})
 * @ORM\Entity
 */
class Cardview
{
    /**
     * @var int
     *
     * @ORM\Column(name="cardViewID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cardviewid;

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
     * @var \Cardstate
     *
     * @ORM\ManyToOne(targetEntity="Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    private $cardstateid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;

    /**
     * @var \Icons
     *
     * @ORM\ManyToOne(targetEntity="Icons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardIconID", referencedColumnName="iconID")
     * })
     */
    private $cardiconid;

    /**
     * @var \Cardcolour
     *
     * @ORM\ManyToOne(targetEntity="Cardcolour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardColourID", referencedColumnName="colourID")
     * })
     */
    private $cardcolourid;


}
