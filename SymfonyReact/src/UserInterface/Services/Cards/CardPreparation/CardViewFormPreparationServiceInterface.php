<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\UserInterface\DTO\CardViewDTO\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\CardFormTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Doctrine\ORM\ORMException;

interface CardViewFormPreparationServiceInterface
{
    /**
     * @throws ORMException
     * @throws SensorTypeBuilderFailureException
     * @throws CardFormTypeNotRecognisedException
     * @throws SensorTypeException
     */
    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface;
}
