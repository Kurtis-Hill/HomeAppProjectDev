<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardViewDTO\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Services\Cards\UsersCardSelectionService\UsersCardSelectionServiceInterface;
use Doctrine\ORM\ORMException;

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
        ReadingTypeQueryFactory $readingTypeQueryFactory,
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
        $sensorTypeObject = $this->findStandardSensorTypeObjectByCardView($cardViewObject);

        if (!$sensorTypeObject instanceof SensorTypeInterface) {
            throw new SensorTypeException(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME);
        }
        $usersCardSelections = $this->usersCardSelectionService->getUsersStandardCardSelections();

        $cardViewFormDTOBuilder = $this->cardViewFormDTOFactory->getCardViewFormBuilderService($cardFormType);

        return $cardViewFormDTOBuilder->makeFormDTO($sensorTypeObject, $cardViewObject,  $usersCardSelections);
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
