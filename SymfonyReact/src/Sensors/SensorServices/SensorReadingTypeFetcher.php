<?php

namespace App\Sensors\SensorServices;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use App\Sensors\Repository\SensorReadingType\ORM\StandardReadingTypeRepository;
use JetBrains\PhpStorm\ArrayShape;

class SensorReadingTypeFetcher
{
    private BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository;

    private StandardReadingTypeRepository $standardReadingTypeRepository;

    public function __construct(
        BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository,
        StandardReadingTypeRepository $standardReadingTypeRepository,
    ) {
        $this->boolReadingBaseSensorRepository = $boolReadingBaseSensorRepository;
        $this->standardReadingTypeRepository = $standardReadingTypeRepository;
    }

    /**
     * @return AbstractBoolReadingBaseSensor[]|StandardReadingTypeRepository[]
     */
    #[ArrayShape([AbstractBoolReadingBaseSensor::class|StandardReadingTypeRepository::class])]
    public function fetchAllSensorReadingTypesBySensor(Sensor $sensor): array
    {
        $boolTypeSensors = $this->boolReadingBaseSensorRepository->findBySensorID($sensor->getSensorID());
        $standardTypeSensors = $this->standardReadingTypeRepository->findBySensorID($sensor->getSensorID());

        return array_merge($boolTypeSensors, $standardTypeSensors);
    }
}
