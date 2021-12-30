<?php

namespace App\UserInterface\Services\Cards\CardViewDTOCreationService;


use App\UserInterface\Factories\CardViewTypeFactories\CardViewDTOFactory;

class CardViewDTOCreationService implements CardViewDTOCreationServiceInterface
{
    private CardViewDTOFactory $cardViewDTOFactory;

    public function __construct(CardViewDTOFactory $cardViewDTOFactory)
    {
        $this->cardViewDTOFactory = $cardViewDTOFactory;
    }

    public function buildCurrentReadingSensorCards(array $sensorData): array
    {
//        dd('here', $sensorData);
        $cardViewDTOBuilder = $this->cardViewDTOFactory->getCardViewBuilderService(CardViewDTOFactory::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD);

        $cardDTOs = [];
        foreach ($sensorData as $sensor) {
            $cardDTOs[] = $cardViewDTOBuilder->makeDTO($sensor);
        }

//        dd($cardDTOs);
        return $cardDTOs;
    }
}
