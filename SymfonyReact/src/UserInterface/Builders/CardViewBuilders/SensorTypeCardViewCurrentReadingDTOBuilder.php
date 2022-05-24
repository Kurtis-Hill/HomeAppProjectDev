<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\DTO\Response\CurrentCardReadingDTO\CurrentReadingSensorTypeCardDTO;
use App\UserInterface\DTO\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataInterface;

class SensorTypeCardViewCurrentReadingDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    public function makeDTO(array $cardData): ?UserViewSensorTypeCardDataInterface
    {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($cardData['sensortype_sensorType']);
        $formattedSensorData = $cardBuilder->formatScalarCardSensorData($cardData);

        $formattedSensorData = array_values(array_filter($formattedSensorData));

        if (empty($formattedSensorData)) {
            return null;
        }

        return new CurrentReadingSensorTypeCardDTO(
            $cardData['sensors_sensorName'],
            $cardData['sensortype_sensorType'],
            $cardData['room_room'],
            $cardData['icons_iconName'],
            $cardData['cardcolour_colour'],
            $cardData['cardview_cardViewID'],
            $formattedSensorData,
        );
    }
}
