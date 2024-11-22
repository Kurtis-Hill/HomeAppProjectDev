<?php

namespace App\Factories\UserInterface\CardViewTypeFactories;

use App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewCurrentReadingDTOBuilder;
use App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewDTOBuilder;
use App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewGraphReadingDTOBuilder;
use App\Exceptions\UserInterface\CardTypeNotRecognisedException;

class CardViewDTOFactory
{
    public const SENSOR_TYPE_CURRENT_READING_SENSOR_CARD = 'sensorTypeCurrentReading';

    public const SENSOR_TYPE_READING_GRAPH_CARD = 'sensorTypeCardSensorGraph';

    private SensorTypeCardViewCurrentReadingDTOBuilder $cardViewCurrentReadingDTOBuilder;

    private SensorTypeCardViewGraphReadingDTOBuilder $cardViewGraphReadingDTOBuilder;

    public function __construct(
        SensorTypeCardViewCurrentReadingDTOBuilder $cardViewCurrentReadingDTOBuilder,
        SensorTypeCardViewGraphReadingDTOBuilder $cardViewGraphReadingDTOBuilder,
    ) {
        $this->cardViewCurrentReadingDTOBuilder = $cardViewCurrentReadingDTOBuilder;
        $this->cardViewGraphReadingDTOBuilder = $cardViewGraphReadingDTOBuilder;
    }

    /**
     * @throws CardTypeNotRecognisedException
     */
    public function getCardViewBuilderService(string $type): SensorTypeCardViewDTOBuilder
    {
        return match ($type) {
            self::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD => $this->cardViewCurrentReadingDTOBuilder,
            self::SENSOR_TYPE_READING_GRAPH_CARD => $this->cardViewGraphReadingDTOBuilder,
            default => throw new CardTypeNotRecognisedException(CardTypeNotRecognisedException::CARD_TYPE_NOT_RECOGNISED)
        };
    }
}
