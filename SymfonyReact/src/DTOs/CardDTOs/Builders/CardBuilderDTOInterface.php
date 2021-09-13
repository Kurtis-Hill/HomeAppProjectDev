<?php


namespace App\DTOs\CardDTOs\Builders;

use App\DTOs\CardDTOs\Sensors\DTOs\AllCardViewDTOInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface CardBuilderDTOInterface
{
    /**
     * @param SensorInterface $sensorData
     * @param array $extraSensorData
     * @return AllCardViewDTOInterface
     */
    public function makeDTO(SensorInterface $sensorData, array $extraSensorData = []);
}
