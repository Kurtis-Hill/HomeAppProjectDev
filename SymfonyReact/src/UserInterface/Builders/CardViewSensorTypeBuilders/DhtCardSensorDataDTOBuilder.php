<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DhtCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[Pure]
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
        $humiditySensorData = $this->buildHumiditySensorData($sensorData);

        return [
             $temperatureSensorData,
             $humiditySensorData
         ];
    }

    #[Pure]
    public function formatCardFormSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
        $humiditySensorData = $this->buildHumiditySensorData($sensorData);

        return [
            $temperatureSensorData,
            $humiditySensorData
        ];
    }
}
