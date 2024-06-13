<?php

namespace App\Services\Sensor\SensorReadingUpdate\CurrentReading;

use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

interface CurrentReadingSensorDataRequestHandlerInterface
{
    public const UPDATE_CURRENT_READING = 'updateCurrentReading';

    public const SEND_UPDATE_CURRENT_READING = 'sendUpdateCurrentReading';

    #[ArrayShape(
        [
            AnalogCurrentReadingUpdateRequestDTO::class,
            HumidityCurrentReadingUpdateRequestDTO::class,
            LatitudeCurrentReadingUpdateRequestDTO::class,
            TemperatureCurrentReadingUpdateRequestDTO::class,
            BoolCurrentReadingUpdateRequestDTO::class,
        ]
    )]
    public function handleCurrentReadingDTOCreation(SensorDataCurrentReadingUpdateRequestDTO $sensorDataCurrentReadingUpdateDTO): array;

    public function getReadingTypeRequestAttempt(): int;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getSuccessfulRequests(): array;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getValidationErrors(): array;
}
