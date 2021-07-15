<?php


namespace App\DTOs\Factorys\CardDTOs;

use App\DTOs\Sensors\CardDTOs\CardViewSensorFormDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface CardFormDTOInterface
{
    /**
     * @param SensorInterface $sensorData
     * @param array $usersCardSelections
     * @return CardViewSensorFormDTO
     */
    public function makeDTO(SensorInterface $sensorData, array $usersCardSelections): CardViewSensorFormDTO;
}
