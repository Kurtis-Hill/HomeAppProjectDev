<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class BmpCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
        $humidSensorData = $this->buildHumiditySensorData($sensorData);
        $latitudeSensorData = $this->buildLatitudeSensorData($sensorData);

        return [
            $temperatureSensorData,
            $humidSensorData,
            $latitudeSensorData,
        ];
    }
}
