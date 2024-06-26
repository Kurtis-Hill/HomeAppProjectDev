<?php

namespace App\DTOs\UserInterface\Response\CurrentCardReadingDTO;

use App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewCurrentReadingDTOBuilder;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CurrentReadingSensorTypeCardResponseDTO implements UserViewSensorTypeCardDataResponseDTOInterface
{
    private string $sensorName;

    private string $sensorType;

    private string $sensorRoom;

    private string $cardIcon;

    private string $cardColour;

    private int $cardViewID;

    #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
    private array $sensorData;

    private string $cardType = SensorTypeCardViewCurrentReadingDTOBuilder::CARD_TYPE;

    public function __construct(
        string $sensorName,
        string $sensorType,
        string $sensorRoom,
        string $cardIcon,
        string $cardColour,
        int $cardViewID,
        array $sensorData,
    ) {
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
        $this->sensorRoom = $sensorRoom;
        $this->cardIcon = $cardIcon;
        $this->cardColour = $cardColour;
        $this->cardViewID = $cardViewID;
        $this->sensorData = $sensorData;

    }

    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getSensorRoom(): string
    {
        return $this->sensorRoom;
    }

    public function getCardIcon(): string
    {
        return $this->cardIcon;
    }

    public function getCardColour(): string
    {
        return $this->cardColour;
    }

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function getCardType(): string
    {
        return $this->cardType;
    }
}
