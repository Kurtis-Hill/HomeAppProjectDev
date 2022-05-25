<?php

namespace App\UserInterface\Controller\Card;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\Exceptions\CardTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\Cards\CardDataFilterService\CardDataFilterService;
use App\UserInterface\Services\Cards\CardPreparation\CardViewPreparationServiceInterface;
use App\UserInterface\Services\Cards\CardViewDTOCreationService\CardViewDTOCreationServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-data/')]
class CardController extends AbstractController
{
    use HomeAppAPITrait;

    private CardDataFilterService $cardDataFilterService;

    private CardViewPreparationServiceInterface $cardPreparationService;

    private CardViewDTOCreationServiceInterface $cardViewDTOCreationService;

    public const ROOM_VIEW = 'room';

    public const DEVICE_VIEW = 'device';

    public function __construct(
        CardDataFilterService $cardDataFilterService,
        CardViewPreparationServiceInterface $cardPreparationService,
        CardViewDTOCreationServiceInterface $cardViewDTOCreationService,
    ) {
        $this->cardDataFilterService = $cardDataFilterService;
        $this->cardPreparationService = $cardPreparationService;
        $this->cardViewDTOCreationService = $cardViewDTOCreationService;
    }

    #[Route('device-cards/{id}', name: 'device-card-data-v2', methods: [Request::METHOD_GET])]
    public function deviceCards(Devices $device, Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_DEVICE_CARD_DATA, $device);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($request);

        $cardViewTypeFilter = new CardViewTypeFilterDTO(null, $device);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::DEVICE_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationService->buildCurrentReadingSensorCards($cardData);
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
        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_ROOM_CARD_DATA, $room);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $cardDatePreFilterDTO = $this->prepareFilters($request);

        $cardViewTypeFilter = new CardViewTypeFilterDTO($room);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter,self::ROOM_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationService->buildCurrentReadingSensorCards($cardData);
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
        $cardDatePreFilterDTO = $this->prepareFilters($request);

        $cardViewTypeFilter = new CardViewTypeFilterDTO();
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Icons filters')]);
        }

        try {
            $cardDTOs = $this->cardViewDTOCreationService->buildCurrentReadingSensorCards($cardData);
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

    /**
     * @throws ORMException|WrongUserTypeException
     */
    private function prepareCardDataForUser(
        CardDataPreFilterDTO $cardDataPreFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array {
        $postFilterCardDataToQuery = $this->cardDataFilterService->filterSensorsToQuery($cardDataPreFilterDTO);

        return $this->cardPreparationService->prepareCardsForUser(
            $this->getUser(),
            $postFilterCardDataToQuery,
            $cardViewTypeFilterDTO,
            $view
        );
    }

    private function prepareFilters(Request $request): CardDataPreFilterDTO
    {
        return $this->cardDataFilterService->preparePreFilterDTO(
            $request->get('sensor-types') ?? [],
            $request->get('reading-types') ?? [],
        );
    }
}
