<?php

namespace App\UserInterface\Controller\Card;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Factories\SensorTypeObjectsBuilderFactory;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-data/')]
class CardViewFormController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('sensor-type/card-sensor-form', name: 'get-card-view-form-v2', methods: [Request::METHOD_GET])]
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

    #[Route('sensor-type/update-card-sensor', name: 'card-view-form-v2', methods: [Request::METHOD_POST])]
    public function updateCardView(
        Request $request,
        CardViewRepositoryInterface $cardViewRepository,
        CardViewUpdateServiceInterface $cardViewUpdateService,
        CardViewFormPreparationServiceInterface $cardViewFormPreparationService,
        SensorReadingTypesValidatorServiceInterface $sensorReadingTypesValidatorService,
        SensorTypeObjectsBuilderFactory $sensorTypeObjectsBuilderFactory,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
    ) {
        try {
            $cardData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->sendBadRequestJsonResponse(['Format not expected']);
        }
        $cardViewID = $cardData['cardViewID'];

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['malformed card view id not recognised']);
        }

        if (empty($cardData['sensorData'])) {
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

        $validationErrors = $cardViewUpdateService->handleStandardCardUpdateRequest($standardCardUpdateDTO, $cardViewObject);
        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse([$validationErrors]);
        }

        try {
            $cardViewRepository->persist($cardViewObject);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $sensorTypeObjectBuilder = $sensorTypeObjectsBuilderFactory->getReadingTypeObjectBuilders(
            $cardViewObject->getSensorNameID()->getSensorTypeObject()->getSensorType()
        );
        $sensorReadingTypeObjectsDTO = $sensorTypeObjectBuilder->buildReadingTypeObjectsDTO();

//dd($sensorReadingTypeObjectsDTO);
        $sensorTypeJoinQueryDTO = $updateSensorBoundaryReadingsService->getReadingTypeObjectJoinQueryDTO($cardViewObject->getSensorNameID()->getSensorTypeObject()->getSensorType());

        try {
            $sensorReadingJoinQueryDTOs = $updateSensorBoundaryReadingsService->getSensorTypeObjectJoinQueryDTO($sensorReadingTypeObjectsDTO);
        } catch (ReadingTypeBuilderFailureException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        $sensorTypeObject = $updateSensorBoundaryReadingsService->findSensorTypeToUpdateBoundaryReadings(
            $sensorTypeJoinQueryDTO,
            $sensorReadingJoinQueryDTOs,
            $cardViewObject->getSensorNameID()
                ->getDeviceObject()
                ->getDeviceNameID(),
            $cardViewObject->getSensorNameID()->getSensorName(),
        );

        $updateReadingDTOs = $updateSensorBoundaryReadingsService->createSensorUpdateBoundaryReadingsDTOs($sensorTypeObject, $cardData['sensorData']);

        $sensorTypeIDDTOs = $sensorTypeObjectBuilder->buildSensorIDReadingTypeUpdateDTO($sensorTypeObject);

//        dd($sensorTypeIDDTOs, $updateReadingDTOs);
        $updateSensorBoundaryReadingsService->setNewBoundaryReadings($sensorTypeObject, $updateReadingDTOs);

//        dd($sensorTypeObject->getTempObject(),$sensorTypeObject->getHumidObject());
//        dd($updateReadingDTOs, $sensorReadingTypeObjectsDTO);
//dd($sensorTypeObject, 'er');
//        dd($sensorReadingJoinQueryDTOs, $sensorTypeObject);
//        try {
////            $sensorTypeObject = $cardViewFormPreparationService->getSensorTypeDataByCardViewObject($cardViewObject);
//        } catch (SensorTypeBuilderFailureException $e) {
//            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
//        } catch (ORMException) {
//            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
//        }
        $sensorReadingTypeErrors = $sensorReadingTypesValidatorService->validateReadingTypeObjects($sensorTypeObject);

        if (empty($sensorReadingTypeErrors)) {
            $cardViewRepository->flush();
        }

        return $this->sendSuccessfulJsonResponse();
//        dd($sensorReadingTypeErrors, 'he');
//        dd($sensorTypeObject, $sensorReadingTypeErrors);
    }
}
