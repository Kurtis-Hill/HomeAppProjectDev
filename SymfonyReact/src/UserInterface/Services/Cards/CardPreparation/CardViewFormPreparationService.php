<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardViewDTO\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Services\Cards\UsersCardSelectionService\UsersCardSelectionServiceInterface;

class CardViewFormPreparationService implements CardViewFormPreparationServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private UsersCardSelectionServiceInterface $usersCardSelectionService;

    private CardViewFormDTOFactory $cardViewFormDTOFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        UsersCardSelectionServiceInterface $usersCardSelectionService,
        CardViewFormDTOFactory $cardViewFormDTOFactory,
    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->usersCardSelectionService = $usersCardSelectionService;
        $this->cardViewFormDTOFactory = $cardViewFormDTOFactory;
    }

    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();

        $sensorReadingTypeJoinQueryDTOs = [];
        foreach ($allSensorTypes as $sensorType) {
            $sensorReadingTypeJoinQueryDTOs[] = $this->prepareSensorTypesQueryBuilder($sensorType);
        }

        $sensorTypeObject = $this->sensorRepository->getSensorReadingTypeCardFormDataBySensor(
            $cardViewObject->getSensorNameID(),
            $sensorReadingTypeJoinQueryDTOs
        );

        if (!$sensorTypeObject instanceof SensorTypeInterface) {
            throw new SensorTypeException(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME);
        }
        $usersCardSelections = $this->usersCardSelectionService->getUsersStandardCardSelections();

        $cardViewFormDTOBuilder = $this->cardViewFormDTOFactory->getCardViewFormBuilderService($cardFormType);

        return $cardViewFormDTOBuilder->makeFormDTO($sensorTypeObject, $cardViewObject,  $usersCardSelections);
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    private function prepareSensorTypesQueryBuilder(SensorType $sensorType): JoinQueryDTO
    {
        $sensorTypeQueryDTOBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType->getSensorType());

        return $sensorTypeQueryDTOBuilder->buildSensorTypeQueryJoinDTO();
    }
}
