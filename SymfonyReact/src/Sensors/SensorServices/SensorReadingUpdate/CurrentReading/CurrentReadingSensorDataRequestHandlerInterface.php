<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading;

use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBoundaryReadingBuilderInterface;
use App\Sensors\Builders\ReadingTypeUpdateBuilders\ReadingTypeUpdateBuilderInterface;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\DTO\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
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
    public function handleCurrentReadingDTOCreation(SensorDataCurrentReadingUpdateDTO $sensorDataCurrentReadingUpdateDTO): array;

    public function getReadingTypeRequestAttempt(): int;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getSuccessfulRequests(): array;

    #[ArrayShape([CurrentReadingUpdateResponseDTO::class])]
    public function getValidationErrors(): array;
}
