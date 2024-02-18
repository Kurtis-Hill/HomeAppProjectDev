<?php

namespace App\UserInterface\Services\Cards\CardViewDTOCreation;

use App\UserInterface\Factories\CardViewTypeFactories\CardViewDTOFactory;

class CardViewDTOCreationHandler
{
    private CardViewDTOFactory $cardViewDTOFactory;

    public function __construct(CardViewDTOFactory $cardViewDTOFactory)
    {
        $this->cardViewDTOFactory = $cardViewDTOFactory;
    }

    public function handleCurrentReadingSensorCardsCreation(array $sensorData): array
    {
        $cardViewDTOBuilder = $this->cardViewDTOFactory->getCardViewBuilderService(CardViewDTOFactory::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD);

        $cardDTOs = [];

        foreach ($sensorData as $sensor) {
            $cardDTO = $cardViewDTOBuilder->buildTrimmedDownSensorTypeCardViewDTO($sensor);
            if ($cardDTO !== null) {
                $cardDTOs[] = $cardDTO;
            }
        }

        return $cardDTOs;
    }
}
