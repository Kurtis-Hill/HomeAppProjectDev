<?php

namespace App\Entity\Sensors\ConstantRecording;


use App\Entity\Sensors\ReadingTypes\Analog;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * ConstAnalog
 *
 * @ORM\Table(name="constanalog", indexes={@ORM\Index(name="sensorID", columns={"sensorID"})})
 * @ORM\Entity
 */
class ConstAnalog implements ConstantlyRecordInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="constRecordID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $constRecordID;

    /**
     * @var float
     *
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    private float $sensorReading;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timez", type="date", nullable=false)
     */
    private DateTime $time;

    /**
     * @var Analog
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorReadingTypeID", referencedColumnName="analogID")
     * })
     */
    private Analog $sensorReadingTypeID;

    /**
     * @return int
     */
    public function getConstRecordID(): int
    {
        return $this->constRecordID;
    }

    /**
     * @param int $constRecordID
     */
    public function setConstRecordID(int $constRecordID): void
    {
        $this->constRecordID = $constRecordID;
    }

    /**
     * @return float
     */
    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    /**
     * @param float $sensorReading
     */
    public function setSensorReading(float $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    /**
     * @return DateTime
     */
    public function getTime(): DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime|null $time
     */
    public function setTime(?DateTime $time = null): void
    {
        $this->time = $time ?? new DateTime('now');
    }

    /**
     * @return Analog
     */
    public function getSensorReadingTypeID(): Analog
    {
        return $this->sensorReadingTypeID;
    }

    /**
     * @param Analog $sensorReadingTypeID
     */
    public function setSensorReadingTypeID(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        $this->sensorReadingTypeID = $sensorReadingTypeID;
    }
}
