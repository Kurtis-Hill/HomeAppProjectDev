<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\DTO\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use JetBrains\PhpStorm\ArrayShape;

interface CurrentReadingSensorDataRequestHandlerInterface
{
    public function processSensorUpdateData(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): bool;

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

    public function getReadingTypeRequestAttempt(): int;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getSuccessfulRequests(): array;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getValidationErrors(): array;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getErrors(): array;
}
