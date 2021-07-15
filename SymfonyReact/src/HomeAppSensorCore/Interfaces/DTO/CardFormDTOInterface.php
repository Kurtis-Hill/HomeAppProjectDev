<?php


namespace App\HomeAppSensorCore\Interfaces\DTO;


use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface CardFormDTOInterface
{
    public function makeDTO(SensorInterface $sensorData, array $usersCardSelections): CardViewSensorFormDTO;
}
