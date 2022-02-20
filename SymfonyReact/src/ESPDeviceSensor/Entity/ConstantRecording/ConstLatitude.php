<?php

namespace App\ESPDeviceSensor\Entity\ConstantRecording;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Constlatitude
 *
 * @ORM\Table(name="constlatitude", indexes={@ORM\Index(name="latitudeID", columns={"latitudeID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryLatitudeRepository")
 */
class ConstLatitude implements ConstantlyRecordInterface
{
    /**
     * @ORM\Column(name="constRecordID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $constRecordID;

    /**
     * @ORM\Column(name="sensorReading", type="integer", nullable=false)
     */
    #[LatitudeConstraint]
    private int|float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'Const latitude date time should not be blank')]
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Latitude")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="latitudeID", referencedColumnName="latitudeID")
     * })
     */
    #[Assert\NotNull(message: "Const Record Latitude Object cannot be null")]
    private Latitude $sensorReadingTypeID;

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

    public function getSensorReading(): float|int
    {
        return $this->sensorReading;
    }

    public function setSensorReading(float|int $sensorReading): void
    {
        $this->sensorReading = $sensorReading;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getSensorReadingTypeObject(): Latitude
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Latitude) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}
