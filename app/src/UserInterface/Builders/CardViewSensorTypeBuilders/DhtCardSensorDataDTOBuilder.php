<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

readonly class DhtCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    public function __construct(
        private TemperatureSensorCardViewDTOBuilder $tempSensorBuilder,
        private HumiditySensorCardViewDTOBuilder $humiditySensorBuilder,
        private SensorReadingTypeRepositoryFactory $sensorTypeRepositoryFactory,
    ) {
        parent::__construct($this->sensorTypeRepositoryFactory);
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class, StandardCardViewReadingResponseDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->tempSensorBuilder->buildTemperatureSensorDataFromScalarArray($sensorData);
        $humiditySensorData = $this->humiditySensorBuilder->buildHumiditySensorDataFromScalarArray($sensorData);

        return [
             $temperatureSensorData,
             $humiditySensorData
         ];
    }
}
