<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\DTOs\CardDTOs\Sensors\DTOs\CardViewSensorFormDTO;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\Entity\Card\CardView;

class CardViewFormPreparationService implements CardViewFormPreparationServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function getCardViewFormDTO(CardView $cardViewObject): ?CardViewSensorFormDTO
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();
        $cardData = $this->sensorRepository->
    }
}
