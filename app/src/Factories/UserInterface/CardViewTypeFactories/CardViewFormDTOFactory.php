<?php

namespace App\Factories\UserInterface\CardViewTypeFactories;

use App\Builders\UserInterface\CardViewDTOBuilders\FormResponse\CardViewFormDTOBuilderInterface;
use App\Builders\UserInterface\CardViewDTOBuilders\FormResponse\StandardCardViewFormDTOBuilder;
use App\Exceptions\UserInterface\CardFormTypeNotRecognisedException;

class CardViewFormDTOFactory
{
    public const SENSOR_TYPE_READING_FORM_CARD = 'sensorTypeReadingForm';

    private StandardCardViewFormDTOBuilder $cardViewFormDTOBuilder;

    public function __construct(
        StandardCardViewFormDTOBuilder $standardCardViewFormDTOBuilder

    ) {
        $this->cardViewFormDTOBuilder = $standardCardViewFormDTOBuilder;
    }

    /**
     * @throws CardFormTypeNotRecognisedException
     */
    public function getCardViewFormBuilderService(string $type): CardViewFormDTOBuilderInterface
    {
        return match ($type) {
            self::SENSOR_TYPE_READING_FORM_CARD => $this->cardViewFormDTOBuilder,
            default => throw new CardFormTypeNotRecognisedException(CardFormTypeNotRecognisedException::MESSAGE)
        };
    }
}
