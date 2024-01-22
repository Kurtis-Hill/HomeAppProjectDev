<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
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
     * @return SensorReadingTypeResponseDTOInterface[]
     * @throws SensorReadingTypeObjectNotFoundException|SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape([StandardReadingTypeResponseInterface::class])]
    public function buildSensorReadingTypeResponseDTOs(Sensor $sensor): array
    {
        $allStandardReadingTypes = $this->sensorReadingTypeFetcher->fetchAllSensorReadingTypesBySensor($sensor);

        $sensorReadingTypeResponseDTOs = [];
        foreach ($allStandardReadingTypes as $readingType) {
//            if ($readingType instanceof Temperature) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($readingType::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[$readingType::getReadingTypeName()] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//            }
//            if ($readingType instanceof Humidity) {
//                $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($readingType::getReadingTypeName());
//                $sensorReadingTypeResponseDTOs[Humidity::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//            }
//            if ($readingType instanceof Latitude) {
//                $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($readingType::getReadingTypeName());
//                $sensorReadingTypeResponseDTOs[Latitude::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//                break;
//            }
//            if ($readingType instanceof Analog) {
//                $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder($readingType::getReadingTypeName());
//                $sensorReadingTypeResponseDTOs[Analog::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//                break;
//            }
//            if ($readingType instanceof Motion) {
//                $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Motion::getReadingTypeName());
//                $sensorReadingTypeResponseDTOs[Motion::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//            }
//            if ($readingType instanceof Relay) {
//                $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Relay::getReadingTypeName());
//                $sensorReadingTypeResponseDTOs[Relay::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($readingType);
//            }
        }

        return $sensorReadingTypeResponseDTOs;
    }
}
