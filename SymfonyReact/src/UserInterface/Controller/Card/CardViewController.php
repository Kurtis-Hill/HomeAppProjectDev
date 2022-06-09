<?php

namespace App\UserInterface\Controller\Card;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\Builders\CardRequestDTOBuilders\CardViewTypeFilterDTOBuilder;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\UserInterface\DTO\RequestDTO\CardViewFilterRequestDTO;
use App\UserInterface\Exceptions\CardTypeNotRecognisedException;
use App\UserInterface\Exceptions\CardViewRequestException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\Cards\CardDataFilter\CardDataFilter;
use App\UserInterface\Services\Cards\CardPreparation\CurrentReadingCardViewPreparationHandler;
use App\UserInterface\Services\Cards\CardViewDTOCreation\CardViewDTOCreationHandler;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-data/')]
class CardViewController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    private CardDataFilter $cardDataFilterService;

    private CurrentReadingCardViewPreparationHandler $cardPreparationService;

    private CardViewDTOCreationHandler $cardViewDTOCreationHandler;

    private ValidatorInterface $validator;

    public const ROOM_VIEW = 'room';

    public const DEVICE_VIEW = 'device';

    public function __construct(
        CardDataFilter $cardDataFilterService,
        CurrentReadingCardViewPreparationHandler $currentReadingCardViewPreparationHandler,
        CardViewDTOCreationHandler $cardViewDTOCreationService,
        ValidatorInterface $validator,
    ) {
        $this->cardDataFilterService = $cardDataFilterService;
        $this->cardPreparationService = $currentReadingCardViewPreparationHandler;
        $this->cardViewDTOCreationHandler = $cardViewDTOCreationService;
        $this->validator = $validator;
    }

    #[Route('device-cards/{id}', name: 'device-card-data-v2', methods: [Request::METHOD_GET])]
    public function deviceCards(Devices $device, Request $request): JsonResponse
    {
        $cardViewRequestDTO = $this->validateRequestDTO($request);

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_DEVICE_CARD_DATA, $device);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO(null, $device);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::DEVICE_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationHandler->handleCurrentReadingSensorCardsCreation($cardData);
        } catch (SensorTypeBuilderFailureException|CardTypeNotRecognisedException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        try {
            $responseData = $this->normalizeResponse($cardDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($responseData);

    }

    #[Route('room-cards/{id}', name: 'room-card-data-v2', methods: [Request::METHOD_GET])]
    public function roomCards(Room $room, Request $request): Response
    {
        $cardViewRequestDTO = $this->validateRequestDTO($request);

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_ROOM_CARD_DATA, $room);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO($room);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter,self::ROOM_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationHandler->handleCurrentReadingSensorCardsCreation($cardData);
        } catch (SensorTypeBuilderFailureException|CardTypeNotRecognisedException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        try {
            $responseData = $this->normalizeResponse($cardDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($responseData);
    }

    #[Route('index', name: 'index-card-data-v2-boom', methods: [Request::METHOD_GET])]
    public function indexCards(Request $request): JsonResponse
    {
        $cardViewRequestDTO = $this->validateRequestDTO($request);

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO();
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException $e) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationHandler->handleCurrentReadingSensorCardsCreation($cardData);
        } catch (SensorTypeBuilderFailureException | CardTypeNotRecognisedException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        try {
            $responseData = $this->normalizeResponse($cardDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($responseData);
    }

    private function validateRequestDTO(Request $request): CardViewFilterRequestDTO
    {
        $cardViewRequestDTO = new CardViewFilterRequestDTO();

        $sensorTypes = $request->get('sensor-types');
        $readingTypes = $request->get('reading-types');

        if ($sensorTypes !== null) {
            $cardViewRequestDTO->setSensorTypes($sensorTypes);
        }
        if ($readingTypes !== null) {
            $cardViewRequestDTO->setReadingTypes($readingTypes);
        }
        if ($request->get('sensor-types')) {
            $cardViewRequestDTO->setSensorTypes($request->get('sensor-types'));
        }

        $requestValidationErrors = $this->validator->validate($cardViewRequestDTO);

        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return new CardViewFilterRequestDTO();
        }

        return $cardViewRequestDTO;
    }

    /**
     * @throws ORMException|WrongUserTypeException
     */
    private function prepareCardDataForUser(
        CardDataPreFilterDTO $cardDataPreFilterDTO,
        CardViewUriFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array {
        $postFilterCardDataToQuery = $this->cardDataFilterService->filterSensorsToQuery($cardDataPreFilterDTO);

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new WrongUserTypeException();
        }

        return $this->cardPreparationService->prepareCardsForUser(
            $this->getUser(),
            $postFilterCardDataToQuery,
            $cardViewTypeFilterDTO,
            $view
        );
    }

    private function prepareFilters(CardViewFilterRequestDTO $cardViewFilterRequestDTO): CardDataPreFilterDTO
    {
        return CardViewTypeFilterDTOBuilder::buildCardDataPreFilterDTO(
            $cardViewFilterRequestDTO->getSensorTypes(),
            $cardViewFilterRequestDTO->getReadingTypes(),
        );
    }
}
