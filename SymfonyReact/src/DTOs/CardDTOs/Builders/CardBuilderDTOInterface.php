<?php


namespace App\DTOs\CardDTOs\Builders;

use App\DTOs\CardDTOs\Sensors\DTOs\AllCardViewDTOInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

interface CardBuilderDTOInterface
{
    /**
     * @param SensorTypeInterface $sensorData
     * @param array $extraSensorData
     * @return AllCardViewDTOInterface
     */
    public function makeDTO(SensorTypeInterface $sensorData, array $extraSensorData = []);
}
