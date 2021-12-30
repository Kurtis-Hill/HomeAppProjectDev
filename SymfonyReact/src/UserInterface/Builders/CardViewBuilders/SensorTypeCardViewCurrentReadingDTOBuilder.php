<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\DTO\CurrentReadingSensorTypeCardData\CurrentReadingSensorTypeCardDataDTO;

class SensorTypeCardViewCurrentReadingDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    public function makeDTO(array $cardData): CurrentReadingSensorTypeCardDataDTO
    {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($cardData['sensortype_sensorType']);
        $formattedSensorData = $cardBuilder->formatCardSensorData($cardData);

        return new CurrentReadingSensorTypeCardDataDTO(
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
