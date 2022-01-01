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
use App\UserInterface\Factories\CardQueryBuilderFactories\ReadingTypeQueryFactory;
use App\UserInterface\Factories\CardQueryBuilderFactories\SensorTypeQueryFactory;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Services\Cards\UsersCardSelectionService\UsersCardSelectionServiceInterface;
use Doctrine\ORM\ORMException;

class CardViewFormPreparationService implements CardViewFormPreparationServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private ReadingTypeQueryFactory $readingTypeQueryFactory;

    private UsersCardSelectionServiceInterface $usersCardSelectionService;

    private CardViewFormDTOFactory $cardViewFormDTOFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        ReadingTypeQueryFactory $readingTypeQueryFactory,
        UsersCardSelectionServiceInterface $usersCardSelectionService,
        CardViewFormDTOFactory $cardViewFormDTOFactory,
    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
        $this->usersCardSelectionService = $usersCardSelectionService;
        $this->cardViewFormDTOFactory = $cardViewFormDTOFactory;
    }

    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface
    {
        $sensorTypeObject = $this->findStandardSensorTypeObjectByCardView($cardViewObject);

        if (!$sensorTypeObject instanceof SensorTypeInterface) {
            throw new SensorTypeException(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME);
        }
        $usersCardSelections = $this->usersCardSelectionService->getUsersStandardCardSelections();

        $cardViewFormDTOBuilder = $this->cardViewFormDTOFactory->getCardViewFormBuilderService($cardFormType);

        return $cardViewFormDTOBuilder->makeFormDTO($sensorTypeObject, $cardViewObject,  $usersCardSelections);
    }

    public function getSensorTypeDataByCardViewObject(CardView $cardView): SensorTypeInterface
    {
//        $sensorTypeName = $cardView->getSensorNameID()->getSensorTypeObject()->getSensorType();
//        $allReadingTypes = ReadingTypes::SENSOR_READING_TYPE_DATA;
//
//        $readingTypesToQuery = $this->readingTypeQueryFactory->getReadingTypeQueryDTOBuilder()
//        return $this->findStandardSensorTypeObjectByCardView($cardView);
    }

    /**
     * @throws SensorTypeBuilderFailureException
     * @throws ORMException
     */
    private function findStandardSensorTypeObjectByCardView(CardView $cardViewObject): SensorTypeInterface
    {
        $allSensorTypes = $this->sensorTypeRepository->findAll();

        $sensorReadingTypeJoinQueryDTOs = [];
        foreach ($allSensorTypes as $sensorType) {
            $sensorReadingTypeJoinQueryDTOs[] = $this->prepareSensorTypesQueryBuilder($sensorType);
        }

        return $this->sensorRepository->getSensorReadingTypeDataBySensor(
            $cardViewObject->getSensorNameID(),
            $sensorReadingTypeJoinQueryDTOs
        );
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
