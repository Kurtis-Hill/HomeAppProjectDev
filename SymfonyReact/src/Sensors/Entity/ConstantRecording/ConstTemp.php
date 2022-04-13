<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConstTemp
 *
 * @ORM\Table(name="consttemp", indexes={@ORM\Index(name="consttemp_ibfk_1", columns={"sensorID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryTempRepository")
 */
class ConstTemp implements ConstantlyRecordInterface
{
    /**
     * @ORM\Column(name="constRecordID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $constRecordID;

    /**
     * @ORM\Column(name="sensorReading", type="float", precision=10, scale=0, nullable=false)
     */
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        )
    ]
    private float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'Const temp date time should not be blank')]
    private DateTimeInterface $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    #[Assert\NotNull(message: "Const Record Temperature Object cannot be null")]
    private Temperature $sensorReadingTypeID;

    public function getConstRecordID(): int
    {
        return $this->constRecordID;
    }

    public function setConstRecordID(int $constRecordID): void
    {
        $this->constRecordID = $constRecordID;
    }

    public function getSensorReading(): float
    {
        return $this->sensorReading;
    }

    public function setSensorReading(float $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->time;
    }

    public function setCreatedAt(): void
    {
        $this->time = new DateTimeImmutable('now');
    }

    public function getSensorReadingTypeObject(): Temperature
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Temperature) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
