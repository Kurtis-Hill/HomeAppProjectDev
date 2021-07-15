<?php


namespace App\HomeAppSensorCore\Interfaces\DTO;


use App\DTOs\Sensors\CurrentReadingCardDataDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface CurrentSensorReadingCardInterface
{
    public function makeDTO(SensorInterface $sensorData): CurrentReadingCardDataDTO;
}
