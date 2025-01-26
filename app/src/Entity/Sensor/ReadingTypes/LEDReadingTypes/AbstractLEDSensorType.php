<?php

namespace App\Entity\Sensor\ReadingTypes\LEDReadingTypes;

use App\DTOs\LED\Internal\LEDCurrentReadingDTO\LEDCurrentReadingDTO;
use App\Entity\Sensor\ReadingTypes\BaseReadingTypeInterface;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use Doctrine\ORM\Mapping as ORM;

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

    
}
