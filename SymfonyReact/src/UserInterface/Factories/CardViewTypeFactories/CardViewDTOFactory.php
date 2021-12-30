<?php

namespace App\UserInterface\Factories\CardViewTypeFactories;

use App\UserInterface\Builders\CardViewBuilders\CardViewFormDTOBuilder;
use App\UserInterface\Builders\CardViewBuilders\SensorTypeCardViewCurrentReadingDTOBuilder;
use App\UserInterface\Builders\CardViewBuilders\SensorTypeCardViewDTOBuilder;
use App\UserInterface\Builders\CardViewBuilders\SensorTypeCardViewGraphReadingDTOBuilder;
use App\UserInterface\Exceptions\CardTypeNotRecognisedException;

class CardViewDTOFactory
{
    public const SENSOR_TYPE_CURRENT_READING_SENSOR_CARD = 'sensorTypeCurrentReading';

    public const SENSOR_TYPE_READING_FORM_CARD = 'sensorTypeReadingForm';

    public const SENSOR_TYPE_READING_GRAPH_CARD = 'sensorTypeCardSensorGraph';

    private SensorTypeCardViewCurrentReadingDTOBuilder $cardViewCurrentReadingDTOBuilder;

    private CardViewFormDTOBuilder $cardViewFormDTOBuilder;

    private SensorTypeCardViewGraphReadingDTOBuilder $cardViewGraphReadingDTOBuilder;

    public function __construct(
        SensorTypeCardViewCurrentReadingDTOBuilder $cardViewCurrentReadingDTOBuilder,
        CardViewFormDTOBuilder $cardViewFormDTOBuilder,
        SensorTypeCardViewGraphReadingDTOBuilder $cardViewGraphReadingDTOBuilder,
    ) {
        $this->cardViewCurrentReadingDTOBuilder = $cardViewCurrentReadingDTOBuilder;
        $this->cardViewFormDTOBuilder = $cardViewFormDTOBuilder;
        $this->cardViewGraphReadingDTOBuilder = $cardViewGraphReadingDTOBuilder;
    }

    /**
     * @throws CardTypeNotRecognisedException
     */
    public function getCardViewBuilderService(string $type): SensorTypeCardViewDTOBuilder
    {
        return match ($type) {
            self::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD => $this->cardViewCurrentReadingDTOBuilder,
            self::SENSOR_TYPE_READING_FORM_CARD => $this->cardViewFormDTOBuilder,
            self::SENSOR_TYPE_READING_GRAPH_CARD => $this->cardViewGraphReadingDTOBuilder,
            default => throw new CardTypeNotRecognisedException(CardTypeNotRecognisedException::CARD_TYPE_NOT_RECOGNISED)
        };
    }
}
