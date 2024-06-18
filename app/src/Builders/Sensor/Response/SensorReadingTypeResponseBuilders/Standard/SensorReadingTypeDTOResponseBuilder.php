<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard;

use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\Standard\StandardReadingTypeResponseInterface;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeResponseFactory;
use App\Services\Sensor\SensorReadingTypeFetcher;
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
     * @return \App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface[]
     *
     * @throws SensorReadingTypeObjectNotFoundException|\App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
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

    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $sensorReadingType): AllSensorReadingTypeResponseDTOInterface
    {
        $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($sensorReadingType::getReadingTypeName());

        return $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorReadingType);
    }
}
