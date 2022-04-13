<?php

namespace App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use JetBrains\PhpStorm\ArrayShape;

interface CurrentReadingSensorDataRequestHandlerInterface
{
    public function handleSensorUpdateRequest(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool;

    #[ArrayShape(
        [
            AnalogCurrentReadingUpdateRequestDTO::class,
            HumidityCurrentReadingUpdateRequestDTO::class,
            LatitudeCurrentReadingUpdateRequestDTO::class,
            TemperatureCurrentReadingUpdateRequestDTO::class,
        ]
    )]
    public function handleCurrentReadingDTOCreation(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): array;

    public function getSensorTypeUpdateDTOBuilder(string $readingType): ?ReadingTypeUpdateBuilderInterface;

    #[ArrayShape(['temperature data accepted for sensor <sensor-name>'])]
    public function getSuccessfulRequests(): array;

    public function getReadingTypeRequestAttempt(): int;

    #[ArrayShape(['validationErrors'])]
    public function getValidationErrors(): array;

    #[ArrayShape(['errors'])]
    public function getErrors(): array;
}
