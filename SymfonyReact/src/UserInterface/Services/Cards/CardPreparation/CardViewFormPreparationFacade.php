<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\Builders\UsersCardSelectionBuilders\UsersCardSelectionBuilder;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use Doctrine\ORM\Exception\ORMException;

class CardViewFormPreparationFacade implements CardViewFormPreparationHandlerInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private UsersCardSelectionBuilder $usersCardSelectionService;

    private CardViewFormDTOFactory $cardViewFormDTOFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorTypeQueryFactory $sensorTypeQueryFactory,
        UsersCardSelectionBuilder $usersCardSelectionService,
        CardViewFormDTOFactory $cardViewFormDTOFactory,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->sensorTypeQueryFactory = $sensorTypeQueryFactory;
        $this->usersCardSelectionService = $usersCardSelectionService;
        $this->cardViewFormDTOFactory = $cardViewFormDTOFactory;
    }

    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface
    {
        $sensorTypeObject = $this->findStandardSensorTypeObjectByCardView($cardViewObject);

        $usersCardSelections = $this->usersCardSelectionService->buildUsersCardSelectionDTOs();

        $cardViewFormDTOBuilder = $this->cardViewFormDTOFactory->getCardViewFormBuilderService($cardFormType);

        return $cardViewFormDTOBuilder->buildFormDTO(
            $sensorTypeObject,
            $cardViewObject,
            $usersCardSelections
        );
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

        return $this->sensorRepository->findSensorReadingTypeDataBySensor(
            $cardViewObject->getSensor(),
            $sensorReadingTypeJoinQueryDTOs
        );
    }

    /**
     * @throws SensorTypeBuilderFailureException
     */
    private function prepareSensorTypesQueryBuilder(AbstractSensorType $sensorType): JoinQueryDTO
    {
        $sensorTypeQueryDTOBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType::getReadingTypeName());

        return $sensorTypeQueryDTOBuilder->buildSensorTypeQueryJoinDTO();
    }
}
