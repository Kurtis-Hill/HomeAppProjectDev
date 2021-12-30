<?php

namespace App\UserInterface\Services\Cards\CardViewDTOCreationService;

interface CardViewDTOCreationServiceInterface
{
    public function buildCurrentReadingSensorCards(array $sensorData);
}
