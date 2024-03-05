<?php

namespace App\Sensors\SensorServices;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
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

    public function fetchReadingTypeBasedOnBaseReadingType(int $baseReadingType): AllSensorReadingTypeInterface
    {
        $readingType = $this->standardReadingTypeRepository->findOneBy(['baseReadingType' => $baseReadingType]);
        if ($readingType === null) {
            $readingType = $this->boolReadingBaseSensorRepository->findOneBy(['baseReadingType' => $baseReadingType]);
        }

        return $readingType;
    }

    public function fetchReadingTypesBaseOnSensor(array $sensors): array
    {
        foreach ($sensors as $sensor) {
            $sensorReadingTypes[] = $this->fetchAllSensorReadingTypesBySensor($sensor);
        }

        return $sensorReadingTypes ?? [];
    }

    public function fetchBaseReadingTypeIDsFromSensors(array $sensors): array
    {
        $sensorReadingTypes = $this->fetchReadingTypesBaseOnSensor($sensors);

        $baseReadingTypeIDs = [];
        foreach ($sensorReadingTypes as $sensorReadingType) {
            foreach ($sensorReadingType as $item) {
                $baseReadingTypeIDs[] = $item->getBaseReadingType()->getBaseReadingTypeID();
            }
        }

        return $baseReadingTypeIDs;
    }
}
