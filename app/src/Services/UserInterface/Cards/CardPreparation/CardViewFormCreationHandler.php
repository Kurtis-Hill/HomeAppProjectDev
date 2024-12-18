<?php

namespace App\Services\UserInterface\Cards\CardPreparation;

use App\Builders\UserInterface\UsersCardSelectionBuilders\UsersCardSelectionBuilder;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\DTOs\UserInterface\Response\CardForms\CardViewSensorFormInterface;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\UserInterface\Card\CardView;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use App\Factories\Sensor\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Factories\UserInterface\CardViewTypeFactories\CardViewFormDTOFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorTypeRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class CardViewFormCreationHandler implements CardViewFormPreparationHandlerInterface
{
    public function __construct(
        private UsersCardSelectionBuilder $usersCardSelectionService,
        private CardViewFormDTOFactory $cardViewFormDTOFactory,
    ) {
    }

    public function createCardViewFormDTO(CardView $cardViewObject, string $cardFormType): CardViewSensorFormInterface
    {
        $sensorTypeObject = $cardViewObject->getSensor()->getSensorTypeObject();
        $usersCardSelections = $this->usersCardSelectionService->buildUsersCardSelectionDTOs();

        $cardViewFormDTOBuilder = $this->cardViewFormDTOFactory->getCardViewFormBuilderService($cardFormType);

        return $cardViewFormDTOBuilder->buildFormDTO(
            $sensorTypeObject,
            $cardViewObject,
            $usersCardSelections
        );
    }

//    /**
//     * @throws SensorTypeBuilderFailureException
//     * @throws ORMException
//     */
//    private function findStandardSensorTypeObjectByCardView(CardView $cardViewObject): SensorTypeInterface
//    {
//        $allSensorTypes = $this->sensorTypeRepository->findAll();
//
//        $sensorReadingTypeJoinQueryDTOs = [];
//        foreach ($allSensorTypes as $sensorType) {
//            $sensorReadingTypeJoinQueryDTOs[] = $this->prepareSensorTypesQueryBuilder($sensorType);
//        }
//
//        return $this->sensorRepository->findSensorReadingTypeDataBySensor(
//            $cardViewObject->getSensor(),
//            $sensorReadingTypeJoinQueryDTOs
//        );
//    }

//    /**
//     * @throws SensorTypeBuilderFailureException
//     */
//    private function prepareSensorTypesQueryBuilder(AbstractSensorType $sensorType): JoinQueryDTO
//    {
//        $sensorTypeQueryDTOBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder($sensorType::getReadingTypeName());
//
//        return $sensorTypeQueryDTOBuilder->buildSensorTypeQueryJoinDTO();
//    }
}
