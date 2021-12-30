<?php

namespace App\UserInterface\Builders\CardViewSensorTypeBuilders;

use App\UserInterface\DTO\CardViewDTO\StandardCardViewDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class DallasSensorDataCardDTOBuilder extends AbstractCardDTOBuilder implements CardSensorDataDTOBuilderInterface
{
    #[ArrayShape([StandardCardViewDTO::class])]
    public function formatCardSensorData(array $sensorData): array
    {
        $temperatureSensorData = $this->buildTemperatureSensorData($sensorData);

        return [
            $temperatureSensorData,
        ];
    }
}
