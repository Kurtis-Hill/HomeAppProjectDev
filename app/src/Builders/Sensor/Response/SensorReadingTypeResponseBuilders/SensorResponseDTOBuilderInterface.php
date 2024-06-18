<?php

namespace App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders;

use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;

interface SensorResponseDTOBuilderInterface
{
    /**
     * @throws \App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException
     */
    public function buildSensorReadingTypeResponseDTO(AllSensorReadingTypeInterface $readingTypeObject): AllSensorReadingTypeResponseDTOInterface;
}
