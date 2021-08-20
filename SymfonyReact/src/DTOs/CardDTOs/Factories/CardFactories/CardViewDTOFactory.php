<?php


namespace App\DTOs\CardDTOs\Factories\CardFactories;

use App\DTOs\CardDTOs\Builders\CardBuilderDTOInterface;
use App\DTOs\CardDTOs\Builders\SensorTypeBuilders\SensorTypeCardViewCurrentReadingDTOBuilder;
use App\DTOs\CardDTOs\Builders\SensorTypeBuilders\SensorTypeCardViewFormDTOBuilder;
use App\DTOs\CardDTOs\Builders\SensorTypeBuilders\SensorTypeCardViewGraphBuilder;
use UnexpectedValueException;

class CardViewDTOFactory
{
    public const SENSOR_TYPE_CURRENT_READING_SENSOR_CARD = 'sensorTypeCurrentReading';

    public const SENSOR_TYPE_READING_FORM_CARD = 'sensorTypeReadingForm';

    public const SENSOR_TYPE_READING_GRAPH_CARD = 'sensorTypeCardSensorGraph';

    /**
     * @param string $type
     * @return CardBuilderDTOInterface
     */
    public function build(string $type): CardBuilderDTOInterface
    {
        switch ($type) {
            case self::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD:
                return new SensorTypeCardViewCurrentReadingDTOBuilder();
                break;
            case self::SENSOR_TYPE_READING_FORM_CARD:
                return new SensorTypeCardViewFormDTOBuilder();
                break;
            case self::SENSOR_TYPE_READING_GRAPH_CARD:
                return new SensorTypeCardViewGraphBuilder();
                break;
        }

        throw new UnexpectedValueException('The type provided to the card factory is not recognised');
    }
}
