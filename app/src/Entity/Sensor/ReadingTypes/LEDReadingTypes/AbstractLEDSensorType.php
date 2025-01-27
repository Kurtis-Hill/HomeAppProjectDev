<?php

namespace App\Entity\Sensor\ReadingTypes\LEDReadingTypes;

use App\DTOs\LED\Internal\LEDCurrentReadingDTO\LEDCurrentReadingDTO;
use App\Entity\Sensor\ReadingTypes\BaseReadingTypeInterface;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Repository\Sensor\SensorReadingType\ORM\LEDReadingBaseSensorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;

#[
    Entity(repositoryClass: LEDReadingBaseSensorRepository::class),
    InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'led'),
    ORM\Index(columns: ["currentReading"], name: "currentReading"),
    ORM\Index(columns: ["constRecord"], name: "constRecord"),
    ORM\Index(columns: ["updatedAt"], name: "updatedAt"),
    ORM\Index(columns: ["ledReadingType"], name: "ledReadingType"),
    ORM\Index(columns: ["sensorID"], name: "sensorID"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    DiscriminatorColumn(name: 'ledReadingType', type: 'string'),
    DiscriminatorMap(
        [
            WS2812B::READING_TYPE => WS2812B::class,
        ]
    )
]
abstract class AbstractLEDSensorType implements BaseReadingTypeInterface
{
    #[
        ORM\Column(name: "readingTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $ledID;

    #[
        ORM\OneToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseReadingTypeID", referencedColumnName: "baseReadingTypeID"),
    ]
    private BaseSensorReadingType $baseReadingType;

    #[ORM\Column(name: "currentReading", type: "json", nullable: false)]
    protected LEDCurrentReadingDTO $currentReading;

    #[ORM\Column(name: "presets", type: "json", nullable: false)]
    protected array $presets;

    #[ORM\Column(name: "selectedPreset", type: "integer", nullable: false)]
    protected int $selectedPreset;

    public function getLedID(): int
    {
        return $this->ledID;
    }

    public function setLedID(int $ledID): void
    {
        $this->ledID = $ledID;
    }

    public function getBaseReadingType(): BaseSensorReadingType
    {
        return $this->baseReadingType;
    }

    public function setBaseReadingType(BaseSensorReadingType $baseReadingType): void
    {
        $this->baseReadingType = $baseReadingType;
    }

    public function getCurrentReading(): LEDCurrentReadingDTO
    {
        return $this->currentReading;
    }

    public function setCurrentReading(LEDCurrentReadingDTO $currentReading): void
    {
        $this->currentReading = $currentReading;
    }

    public function getPresets(): array
    {
        return $this->presets;
    }

    public function setPresets(array $presets): void
    {
        $this->presets = $presets;
    }

    public function getSelectedPreset(): int
    {
        return $this->selectedPreset;
    }

    public function setSelectedPreset(int $selectedPreset): void
    {
        $this->selectedPreset = $selectedPreset;
    }
}
