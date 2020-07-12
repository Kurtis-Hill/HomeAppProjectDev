<?php

namespace App\Entity\Card;

use App\Entity\Card\Cardcolour;
use App\Entity\Core\Icons;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\Entity\Core\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", indexes={@ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="cardColour", columns={"cardColourID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"}), @ORM\Index(name="SensorName", columns={"sensorNameID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="cardIcon", columns={"cardIconID"}), @ORM\Index(name="cardview_state", columns={"cardStateID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardviewRepository")
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
     * @var Cardcolour
     *
     * @ORM\ManyToOne(targetEntity="Cardcolour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardColourID", referencedColumnName="colourID")
     * })
     */
    private $cardcolourid;

    /**
     * @var Icons
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Icons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardIconID", referencedColumnName="iconID")
     * })
     */
    private $cardiconid;

    /**
     * @var Cardshow
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    private $cardstateid;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var Sensornames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Sensornames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;

    public function getCardviewid(): ?int
    {
        return $this->cardviewid;
    }

    public function getCardcolourid(): ?Cardcolour
    {
        return $this->cardcolourid;
    }

    public function setCardcolourid(?Cardcolour $cardcolourid): self
    {
        $this->cardcolourid = $cardcolourid;

        return $this;
    }

    public function getCardiconid()
    {
        //dd($this->cardiconid);
        return $this->cardiconid;
    }

    public function setCardiconid(Icons $cardiconid): self
    {
        $this->cardiconid = $cardiconid;

        return $this;
    }

    public function getRoomid(): ?Room
    {
        return $this->roomid;
    }

    public function setRoomid(?Room $roomid): self
    {
        $this->roomid = $roomid;

        return $this;
    }

    public function getcardstateid(): Cardstate
    {
        return $this->cardstateid;
    }

    public function getSensornameid(): ?Sensornames
    {
        return $this->sensornameid;
    }

    public function setcardstateid(?Cardstate $cardstateid): self
    {
        $this->cardstateid = $cardstateid;

        return $this;
    }

    public function setSensornameid(?Sensornames $sensornameid): self
    {
        $this->sensornameid = $sensornameid;

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }


}
