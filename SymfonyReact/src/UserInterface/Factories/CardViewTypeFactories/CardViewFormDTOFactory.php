<?php

namespace App\UserInterface\Factories\CardViewTypeFactories;

use App\UserInterface\Builders\CardViewDTOBuilders\FormResponse\CardViewFormDTOBuilderInterface;
use App\UserInterface\Builders\CardViewDTOBuilders\FormResponse\StandardCardViewFormDTOBuilder;
use App\UserInterface\Exceptions\CardFormTypeNotRecognisedException;

class CardViewFormDTOFactory
{
    public const SENSOR_TYPE_READING_FORM_CARD = 'sensorTypeReadingForm';

    private StandardCardViewFormDTOBuilder $cardViewFormDTOBuilder;

    public function __construct(StandardCardViewFormDTOBuilder $cardViewFormDTOBuilder,)
    {
        $this->cardViewFormDTOBuilder = $cardViewFormDTOBuilder;
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
