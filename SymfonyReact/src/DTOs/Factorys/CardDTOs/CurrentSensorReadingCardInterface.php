<?php


namespace App\DTOs\Factorys\CardDTOs;

use App\DTOs\Sensors\CardDTOs\CurrentReadingCardDataDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface CurrentSensorReadingCardInterface
{
    public function makeDTO(SensorInterface $sensorData): CurrentReadingCardDataDTO;
}
