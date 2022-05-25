<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\Sensors\Exceptions\SensorTypeException;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\CardFormTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Doctrine\ORM\Exception\ORMException;

interface CardViewFormPreparationServiceInterface
{
    /**
     * @throws ORMException
     * @throws SensorTypeBuilderFailureException
     * @throws CardFormTypeNotRecognisedException
     * @throws SensorTypeException
     */
    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface;

    /**
     * @throws SensorTypeBuilderFailureException
     * @throws ORMException
     */
//    public function getSensorTypeDataByCardViewObject(CardView $cardView): SensorTypeInterface;
}
