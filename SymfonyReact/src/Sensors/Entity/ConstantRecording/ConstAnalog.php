<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ConstAnalog
 *
 * @ORM\Table(name="constanalog", indexes={@ORM\Index(name="sensorID", columns={"analogID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\ConstRecord\ConstantlyRecordRepositoryAnalogRepository")
 */
class ConstAnalog implements ConstantlyRecordInterface
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
    #[SoilConstraint(groups: [Soil::NAME])]
    private float $sensorReading;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false)
     */
    #[Assert\NotBlank(message: 'Const analog date time should not be blank')]
    private DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    #[Assert\NotNull(message: "Const Record Analog Object cannot be null")]
    private Analog $sensorReadingTypeID;

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
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getSensorReadingTypeObject(): Analog
    {
        return $this->sensorReadingTypeID;
    }

    public function setSensorReadingTypeObject(AllSensorReadingTypeInterface $sensorReadingTypeID): void
    {
        if ($sensorReadingTypeID instanceof Analog) {
            $this->sensorReadingTypeID = $sensorReadingTypeID;
        }
    }
}