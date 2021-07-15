<?php


namespace App\HomeAppSensorCore\Interfaces\DTO;


use App\DTOs\Sensors\CurrentReadingCardDataDTO;

interface CurrentSensorReadingCardInterface
{
    public function makeDTO(): CurrentReadingCardDataDTO;
}
