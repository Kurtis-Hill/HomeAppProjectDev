<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse;

use App\DTOs\UserInterface\Response\CurrentCardReadingDTO\CurrentReadingSensorTypeCardResponseDTO;
use App\DTOs\UserInterface\Response\CurrentCardReadingDTO\UserViewSensorTypeCardDataResponseDTOInterface;

class SensorTypeCardViewCurrentReadingDTOBuilder extends AbstractSensorTypeViewDTOBuilder implements SensorTypeCardViewDTOBuilder
{
    public const CARD_TYPE = 'current-reading';

    public function buildTrimmedDownSensorTypeCardViewDTO(array $cardData): ?UserViewSensorTypeCardDataResponseDTOInterface
    {
        $cardBuilder = $this->sensorTypeDTOBuilderFactory->getSensorDataDTOBuilderService($cardData['sensortype_sensorType']);
        $formattedSensorData = $cardBuilder->formatScalarCardSensorData($cardData);

        $formattedSensorData = array_values(array_filter($formattedSensorData));
        if (empty($formattedSensorData)) {
            return null;
        }

        return new CurrentReadingSensorTypeCardResponseDTO(
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
