<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Analog
 *
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="analog_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="analog_ibfk_6", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\AnalogRepository")
 */
class Analog implements StandardReadingSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $analogID;

    /**
     * @var float
     *
     * @ORM\Column(name="analogReading", type="smallint", precision=10, scale=0, nullable=false)
     */
    private int $analogReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highAnalog", type="smallint", precision=10, scale=0, nullable=false, options={"default"="1111"})
     */
    private int $highAnalog;

    /**
     * @var float
     *
     * @ORM\Column(name="lowAnalog", type="smallint", precision=10, scale=0, nullable=false, options={"default"="2222"})
     */
    private int $lowAnalog;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true, options={"default"="0"})
     */
    private bool $constRecord = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private ?\DateTime $time;


    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensorNameID;


    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Devices\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private Devices $deviceNameID;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->analogID;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->analogID = $analogid;
    }


    /**
     * Sensor relational Objects
     */


    /**
     * @return Sensors
     */
    public function getSensorObject(): Sensors
    {
        return $this->sensorNameID;
    }

    /**
     * @return Devices
     */
    public function getDeviceObject(): Devices
    {
        return $this->deviceNameID;
    }


    /**
     * @param Sensors $sensorNameID
     */
    public function setSensorNameID(Sensors $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    /**
     * @param Devices $deviceNameID
     */
    public function setDeviceNameID(Devices $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }


    /**
     * Sensor Reading Methods
     */

    /**
     * @return int|float
     */
    public function getCurrentReading(): int|float
    {
        return $this->analogReading;
    }

    /**
     * @return int|float
     */
    public function getHighReading(): int|float
    {
        return $this->highAnalog;
    }

    /**
     * @return float
     */
    public function getLowReading(): int|float
    {
        return $this->lowAnalog;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param int|float $reading
     */
    public function setCurrentSensorReading(int|float $reading): void
    {
        $this->analogReading = $reading;
    }

    /**
     * @param int|float $reading
     */
    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highAnalog = $reading;
        }
    }

    /**
     * @param int|float $reading
     */
    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowAnalog = $reading;
        }
    }

    /**
     * @param \DateTime|null $time
     */
    public function setTime(?\DateTime $time = null): void
    {
        if ($time === null) {
            $time = new \DateTime('now');
        }

        $this->time = $time;
    }

    /**
     * Sensor Functional Methods
     */

    /**
     * @return bool
     */
    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    /**
     * @param bool $constrecord
     */
    public function setConstRecord(bool $constrecord): void
    {
        $this->constRecord = $constrecord;
    }

    #[Pure] public function getMeasurementDifferenceHighReading(): int|float
    {
        return $this->getHighReading() - $this->getCurrentReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getLowReading() - $this->getCurrentReading();
    }

    public function isReadingOutOfBounds(): bool
    {
        if ($this->getCurrentReading() <= $this->getHighReading()) {
            return true;
        }
        if ($this->getCurrentReading() <= $this->getLowReading()) {
            return true;
        }

        return false;
    }
}