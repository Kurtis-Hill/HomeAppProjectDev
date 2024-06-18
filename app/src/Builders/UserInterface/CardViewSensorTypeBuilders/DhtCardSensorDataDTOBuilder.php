<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\HumiditySensorCardViewDTOBuilder;
use App\Builders\UserInterface\CardViewReadingTypeDTOBuilders\Standard\TemperatureSensorCardViewDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
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
