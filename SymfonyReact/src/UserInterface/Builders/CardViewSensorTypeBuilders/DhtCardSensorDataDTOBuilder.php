<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DhtCardDTOBuilder extends AbstractCardDTOBuilder implements CardDTOBuilderInterface
{
    #[Pure]
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatSensorData(array $sensorData): array
    {
         $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);
         $humiditySensorData = $this->buildHumiditySensorData($sensorData);

         return [
             $temperatureSensorData,
             $humiditySensorData
         ];
    }
}
