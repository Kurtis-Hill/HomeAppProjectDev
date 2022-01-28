<?php

namespace App\UserInterface\Controller\Card;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\UserInterface\DTO\CardUpdateDTO\StandardCardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\CardFormTypeNotRecognisedException;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use App\UserInterface\Services\Cards\CardPreparation\CardViewFormPreparationServiceInterface;
use App\UserInterface\Services\Cards\CardViewUpdateService\CardViewUpdateServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-form-data/sensor-type/')]
class CardViewFormController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('card-sensor-form', name: 'get-card-view-form-v2', methods: [Request::METHOD_GET])]
    public function getCardViewForm(
        Request $request,
        CardViewRepositoryInterface $cardViewRepository,
        CardViewFormPreparationServiceInterface $cardViewFormPreparationService,
    ): JsonResponse {
        $cardViewID = $request->query->get('card-view-id');

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $cardViewObject = $cardViewRepository->findOneById($cardViewID);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Card view')]);
        }
        if (!$cardViewObject instanceof CardView) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'CardView')]);
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_VIEW_CARD_VIEW_FORM, $cardViewObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $cardViewFormDTO = $cardViewFormPreparationService->createCardViewFormDTO(
                $cardViewObject,
                CardViewFormDTOFactory::SENSOR_TYPE_READING_FORM_CARD
            );
        } catch (
            SensorTypeException
            | CardFormTypeNotRecognisedException
            | SensorTypeBuilderFailureException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['Query failure']);
        }

        try {
            $normalizedResponseData = $this->normalizeResponse($cardViewFormDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponseData);
    }

    #[Route('update-card-sensor', name: 'card-view-form-v2', methods: [Request::METHOD_PUT])]
    public function updateCardView(
        Request $request,
        CardViewRepositoryInterface $cardViewRepository,
        CardViewUpdateServiceInterface $cardViewUpdateService,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
    ): JsonResponse {
        try {
            $cardData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }
        $cardViewID = $cardData['cardViewID'];

        if (empty($cardData['sensorData']) || empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $cardViewObject = $cardViewRepository->findOneById($cardViewID);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        if (!$cardViewObject instanceof CardView) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'CardView')]);
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_EDIT_CARD_VIEW_FORM, $cardViewObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $standardCardUpdateDTO = new StandardCardUpdateDTO(
            $cardData['cardColour'],
            $cardData['cardIcon'],
            $cardData['cardViewState'],
        );

        $validationErrors = $cardViewUpdateService->updateAllCardViewObjectProperties($standardCardUpdateDTO, $cardViewObject);
        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        try {
            $cardViewRepository->persist($cardViewObject);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

//        // MOVE THIS OUT TO A ESP SENSOR ENDPOINT
//        foreach ($cardData['sensorData'] as $updateData) {
//            try {
//                $updateSensorBoundaryReadingsDTO = $updateSensorBoundaryReadingsService->createUpdateSensorBoundaryReadingDTO($updateData);
//
//                $readingTypeQueryDTOs[] = $updateSensorBoundaryReadingsService->createReadingTypeQueryDTO($updateSensorBoundaryReadingsDTO);
//                $updateSensorBoundaryReadingsDTOs[] = $updateSensorBoundaryReadingsDTO;
//            } catch (ReadingTypeBuilderFailureException) {
//                $updateReadingDTOErrors[] = $updateData['sensorType'] ?? 'no sensor type provided'  . ' is not a valid sensor type';
//            }
//        }
//
//        if (empty($updateSensorBoundaryReadingsDTOs) || empty($readingTypeQueryDTOs)) {
//            return $this->sendBadRequestJsonResponse(['Could not prepare sensor reading data']);
//        }
//
//        try {
//            $sensorTypeJoinQueryDTO = $updateSensorBoundaryReadingsService->getReadingTypeObjectJoinQueryDTO($cardViewObject->getSensorNameID()->getSensorTypeObject()->getSensorType());
//        } catch (SensorTypeBuilderFailureException $e) {
//            return $this->sendInternalServerErrorJsonResponse([$e->getMessage()]);
//        }
//
//        $sensorReadingTypeObjects = $updateSensorBoundaryReadingsService->findSensorAndReadingTypesToUpdateBoundaryReadings(
//             $sensorTypeJoinQueryDTO,
//            $readingTypeQueryDTOs,
//             $cardViewObject->getSensorNameID()
//                 ->getDeviceObject()
//                 ->getDeviceNameID(),
//             $cardViewObject->getSensorNameID()->getSensorName(),
//         );
//
//        $sensorObject = array_pop($sensorReadingTypeObjects);
//
//        if (!$sensorObject instanceof StandardSensorTypeInterface) {
//            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
//        }
//        $validationErrors = $updateSensorBoundaryReadingsService->processBoundaryReadingDTOs($updateSensorBoundaryReadingsDTOs, $sensorReadingTypeObjects, $sensorObject->getSensorTypeName());
//
//        if (!empty($validationErrors)) {
//            return $this->sendBadRequestJsonResponse($validationErrors);
//        }
//
//        if (!empty($updateReadingDTOErrors)) {
//            return $this->sendMultiStatusJsonResponse([$updateReadingDTOErrors]);
//        }

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
