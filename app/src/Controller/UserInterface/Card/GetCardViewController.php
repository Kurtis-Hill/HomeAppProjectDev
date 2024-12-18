<?php

namespace App\Controller\UserInterface\Card;

use App\Builders\Sensor\Internal\SensorFilterDTOBuilders\SensorFilterDTOBuilder;
use App\Builders\UserInterface\CardRequestDTOBuilders\CardViewTypeFilterDTOBuilder;
use App\DTOs\Sensor\Internal\Sensor\SensorFilterDTO;
use App\DTOs\UserInterface\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\DTOs\UserInterface\RequestDTO\CardViewFilterRequestDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Exceptions\UserInterface\CardTypeNotRecognisedException;
use App\Exceptions\UserInterface\CardViewRequestException;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use App\Exceptions\UserInterface\WrongUserTypeException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\SensorFilter\SensorFilter;
use App\Services\UserInterface\Cards\CardPreparation\CurrentReadingCardViewPreparationHandler;
use App\Services\UserInterface\Cards\CardViewDTOCreation\CardViewDTOCreationHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\CardViewVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'cards/')]
class GetCardViewController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    public const ROOM_VIEW = 'room';

    public const DEVICE_VIEW = 'device';

    public const GROUP_VIEW = 'group';

    private SensorFilter $cardDataFilterService;

    private CurrentReadingCardViewPreparationHandler $cardPreparationService;

    private CardViewDTOCreationHandler $cardViewDTOCreationHandler;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    public function __construct(
        SensorFilter $cardDataFilterService,
        CurrentReadingCardViewPreparationHandler $currentReadingCardViewPreparationHandler,
        CardViewDTOCreationHandler $cardViewDTOCreationService,
        ValidatorInterface $validator,
        LoggerInterface $elasticLogger,
    ) {
        $this->cardDataFilterService = $cardDataFilterService;
        $this->cardPreparationService = $currentReadingCardViewPreparationHandler;
        $this->cardViewDTOCreationHandler = $cardViewDTOCreationService;
        $this->validator = $validator;
        $this->logger = $elasticLogger;
    }

    #[Route('device/{id}', name: 'device-card-data-v2', methods: [Request::METHOD_GET])]
    public function deviceCards(Devices $device, Request $request): JsonResponse
    {
        try {
            $cardViewRequestDTO = $this->validateRequestDTO($request);
        } catch (CardViewRequestException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrorsArray());
        }
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
        } catch (ORMException $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Card filters'), ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Card filters')]);
        }

        return $this->commonCardDTOResponse($cardData);
    }

    #[Route('room/{id}', name: 'room-card-data-v2', methods: [Request::METHOD_GET])]
    public function roomCards(Room $room, Request $request): Response
    {
        try {
            $cardViewRequestDTO = $this->validateRequestDTO($request);
        } catch (CardViewRequestException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrorsArray());
        }
        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_ROOM_CARD_DATA, $room);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO($room);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::ROOM_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Card filters'), ['user' => $this->getUser()->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Cards')]);
        }

        return $this->commonCardDTOResponse($cardData);
    }

    #[Route('group/{id}', name: 'group-card-data-v2', methods: [Request::METHOD_GET])]
    public function groupCards(Group $group, Request $request): Response
    {
        try {
            $cardViewRequestDTO = $this->validateRequestDTO($request);
        } catch (CardViewRequestException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrorsArray());
        }
        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_GROUP_CARD_DATA, $group);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO(cardViewTypeFilterGroup: $group);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::GROUP_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Card filters'), ['user' => $this->getUser()->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Cards')]);
        }

        return $this->commonCardDTOResponse($cardData);
    }

    #[Route('index', name: 'index-card-data', methods: [Request::METHOD_GET])]
    public function indexCards(Request $request): JsonResponse
    {
        try {
            $cardViewRequestDTO = $this->validateRequestDTO($request);
        } catch (CardViewRequestException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrorsArray());
        }

        $cardDatePreFilterDTO = $this->prepareFilters($cardViewRequestDTO);

        $cardViewTypeFilter = CardViewTypeFilterDTOBuilder::buildCardViewTypeFilterDTO();
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException $e) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Card filters'), ['user' => $this->getUser()?->getUserIdentifier()]);
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Card')]);
        }

        return $this->commonCardDTOResponse($cardData);
    }

    /**
     * @throws CardViewRequestException
     */
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

        $requestValidationErrors = $this->validator->validate($cardViewRequestDTO);

        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            throw new CardViewRequestException($this->getValidationErrorAsArray($requestValidationErrors));
        }

        return $cardViewRequestDTO;
    }

    /**
     * @throws ORMException|WrongUserTypeException
     */
    private function prepareCardDataForUser(
        SensorFilterDTO $cardDataPreFilterDTO,
        CardViewUriFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array {
        $postFilterCardDataToQuery = $this->cardDataFilterService->filterSensorsToQuery($cardDataPreFilterDTO);

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new WrongUserTypeException();
        }

        return $this->cardPreparationService->prepareCardsForUser(
            $user,
            $postFilterCardDataToQuery,
            $cardViewTypeFilterDTO,
            $view
        );
    }

    private function prepareFilters(CardViewFilterRequestDTO $cardViewFilterRequestDTO): SensorFilterDTO
    {
        return SensorFilterDTOBuilder::buildCardDataPreFilterDTO(
            $cardViewFilterRequestDTO->getSensorTypes(),
            $cardViewFilterRequestDTO->getReadingTypes(),
        );
    }

    /**
     * @param array $cardData
     * @return JsonResponse
     */
    private function commonCardDTOResponse(array $cardData): JsonResponse
    {
        try {
            $cardDTOs = $this->cardViewDTOCreationHandler->handleCurrentReadingSensorCardsCreation($cardData);
        } catch (SensorTypeBuilderFailureException|CardTypeNotRecognisedException $exception) {
            $this->logger->error($exception->getMessage(), ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        }

        try {
            $responseData = $this->normalize($cardDTOs);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($responseData);
    }
}
