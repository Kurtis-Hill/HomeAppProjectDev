<?php

namespace App\Services\UserInterface\Cards\CardPreparation;

use App\DTOs\UserInterface\Response\CardForms\CardViewSensorFormInterface;
use App\Entity\UserInterface\Card\CardView;
use App\Exceptions\UserInterface\CardFormTypeNotRecognisedException;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use Doctrine\ORM\Exception\ORMException;

interface CardViewFormPreparationHandlerInterface
{
    /**
     * @throws ORMException
     * @throws \App\Exceptions\UserInterface\SensorTypeBuilderFailureException
     * @throws \App\Exceptions\UserInterface\CardFormTypeNotRecognisedException
     * @throws \App\Exceptions\Sensor\SensorTypeException
     */
    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface;
}
