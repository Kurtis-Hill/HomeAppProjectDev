<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DhtCardSensorDataDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatScalarCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
        $humiditySensorData = $this->buildHumiditySensorData($sensorData);

        return [
             $temperatureSensorData,
             $humiditySensorData
         ];
    }

    #[Pure]
    public function formatObjectFormSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
        $humiditySensorData = $this->buildHumiditySensorData($sensorData);

        return [
            $temperatureSensorData,
            $humiditySensorData
        ];
    }
}
