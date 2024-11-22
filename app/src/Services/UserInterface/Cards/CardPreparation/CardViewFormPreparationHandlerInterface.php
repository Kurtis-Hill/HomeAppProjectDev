<?php

namespace App\Services\UserInterface\Cards\CardPreparation;

use App\DTOs\UserInterface\Response\CardForms\CardViewSensorFormInterface;
use App\Entity\UserInterface\Card\CardView;
use App\Exceptions\Sensor\SensorTypeException;
use App\Exceptions\UserInterface\CardFormTypeNotRecognisedException;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use Doctrine\ORM\Exception\ORMException;

interface CardViewFormPreparationHandlerInterface
{
    /**
     * @throws ORMException
     * @throws SensorTypeBuilderFailureException
     * @throws CardFormTypeNotRecognisedException
     * @throws SensorTypeException
     */
    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface;
}
