<?php

namespace App\UserInterface\Factories\CardViewTypeFactories;

use App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewCurrentReadingDTOBuilder;
use App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewDTOBuilders\CurrentReadingResponse\SensorTypeCardViewGraphReadingDTOBuilder;
use App\UserInterface\Exceptions\CardTypeNotRecognisedException;

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
