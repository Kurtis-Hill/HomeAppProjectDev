<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use JetBrains\PhpStorm\ArrayShape;

interface CurrentReadingSensorDataRequestHandlerInterface
{
    public function validateSensorDataRequest(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool;

    public function getSensorTypeUpdateDTOBuilder(string $readingType): ?ReadingTypeUpdateBuilderInterface;

    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function checkSensorReadingTypeIsAllowed(string $readingType, string $sensorType): bool;

    public function validateSensorTypeDTO(AbstractCurrentReadingUpdateRequestDTO $currentReadingUpdateRequestDTO, string $sensorType): bool;

    #[ArrayShape(['validationErrors'])]
    public function getValidationErrors(): array;

    #[ArrayShape(['errors'])]
    public function getErrors(): array;
}
