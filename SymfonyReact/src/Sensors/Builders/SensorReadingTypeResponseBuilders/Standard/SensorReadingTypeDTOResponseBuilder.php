<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeResponseFactory;
use App\Sensors\SensorServices\SensorReadingTypeFetcher;
use JetBrains\PhpStorm\ArrayShape;

class SensorReadingTypeDTOResponseBuilder
{
    private SensorReadingTypeResponseFactory $sensorReadingTypeResponseFactory;

    private SensorReadingTypeFetcher $sensorReadingTypeFetcher;

    public function __construct(
        SensorReadingTypeResponseFactory $sensorReadingTypeResponseFactory,
        SensorReadingTypeFetcher $sensorReadingTypeFetcher,
    ) {
        $this->sensorReadingTypeResponseFactory = $sensorReadingTypeResponseFactory;
        $this->sensorReadingTypeFetcher = $sensorReadingTypeFetcher;
    }

    /**
     * @return AllSensorReadingTypeResponseDTOInterface[]
     *
     * @throws SensorReadingTypeObjectNotFoundException|SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape([StandardReadingTypeResponseInterface::class])]
    public function buildSensorReadingTypeResponseDTOs(Sensor $sensor): array
    {
        $allStandardReadingTypes = $this->sensorReadingTypeFetcher->fetchAllSensorReadingTypesBySensor($sensor);

        $sensorReadingTypeResponseDTOs = [];
        foreach ($allStandardReadingTypes as $readingType) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($readingType::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[$readingType::getReadingTypeName()] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
        }

        return $sensorReadingTypeResponseDTOs;
    }
}
