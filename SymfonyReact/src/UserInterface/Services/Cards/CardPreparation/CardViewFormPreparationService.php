<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\Response\CardForms\CardViewSensorFormInterface;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Services\Cards\UsersCardSelectionService\UsersCardSelectionServiceInterface;
use Doctrine\ORM\Exception\ORMException;

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

        $usersCardSelections = $this->usersCardSelectionService->getUsersCardSelectionAsDTOs();

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
