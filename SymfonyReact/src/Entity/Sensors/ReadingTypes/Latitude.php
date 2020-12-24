<?php

namespace App\Entity\Sensors\ReadingTypes;

use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * Latitude
 *
 * @ORM\Table(name="latitude", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"}), @ORM\UniqueConstraint(name="deviceNameID", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\LatitudeRepository")
 */
class Latitude implements StandardSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="latitudeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $latitudeID;

    /**
     * @var int|float
     *
     * @ORM\Column(name="latitude", type="integer", nullable=false)
     */
    private int|float $latitude;

    /**
     * @var int|float
     *
     * @ORM\Column(name="highLatitude", type="integer", nullable=false)
     */
    private int|float $highLatitude;

    /**
     * @var int|float
     *
     * @ORM\Column(name="lowLatitude", type="integer", nullable=false)
     */
    private int|float $lowLatitude;

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private bool $constRecord;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private Devices $deviceNameID;

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
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private \DateTime $timez;

    /**
     * @return int
     */
    public function getSensorID(): int
    {
        return $this->latitudeID;
    }

    /**
     * @param int $analogid
     */
    public function setSensorID(int $analogid): void
    {
        $this->latitudeID = $analogid;
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
     * @param Sensors $id
     */
    public function setSensorNameID(Sensors $id): void
    {
        $this->sensorNameID = $id;
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
     * @return float|int
     */
    public function getCurrentSensorReading(): int|float
    {
        return $this->latitude;
    }

    /**
     * @return float|int
     */
    public function getHighReading(): int|float
    {
        return $this->highLatitude;
    }

    /**
     * @return float|int
     */
    public function getLowReading(): int|float
    {
        return $this->lowLatitude;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->timez;
    }

    /**
     * @param float|int $reading
     */
    public function setCurrentSensorReading(int|float $reading): void
    {
        $this->latitude = $reading;
    }

    /**
     * @param float|int $reading
     */
    public function setHighReading(int|float $reading): void
    {
        $this->highLatitude = $reading;
    }

    /**
     * @param float|int $reading
     */
    public function setLowReading(int|float $reading): void
    {
        $this->lowLatitude = $reading;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setTime(\DateTime $dateTime): void
    {
        $this->timez = $dateTime;
    }

    /**
     * Sensor Functional Methods
     */

    /**
     * @return bool|null
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
        return $this->getCurrentSensorReading() - $this->getHighReading();
    }

    #[Pure] public function getMeasurementDifferenceLowReading(): int|float
    {
        return $this->getCurrentSensorReading() - $this->getLowReading();
    }
}
