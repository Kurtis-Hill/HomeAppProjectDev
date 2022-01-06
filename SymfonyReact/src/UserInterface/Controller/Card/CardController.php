<?php

namespace App\UserInterface\Controller\Card;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\Room;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\Exceptions\CardTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\Cards\CardDataFilterService\CardDataFilterService;
use App\UserInterface\Services\Cards\CardPreparation\CardViewPreparationServiceInterface;
use App\UserInterface\Services\Cards\CardViewDTOCreationService\CardViewDTOCreationServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . '/v2/card-data')]
class CardController extends AbstractController
{
    use HomeAppAPIResponseTrait;

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

    #[Route('device-cards', name: 'device-card-data-v2', methods: [Request::METHOD_GET])]
    public function deviceCards(Request $request, DeviceRepositoryInterface $deviceRepository): Response
    {
        $deviceId = $request->get('device-id');

        if (!is_numeric($deviceId)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }
        try {
            $device = $deviceRepository->findOneById($deviceId);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['An error occurred while retrieving device data']);
        }

        if (!$device instanceof Devices) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Device')]);
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_DEVICE_CARD_DATA, $device);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $cardDatePreFilterDTO = $this->prepareFilters($request);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request not formatted correctly']);
        }

        $cardViewTypeFilter = new CardViewTypeFilterDTO(null, $device);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::DEVICE_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Card filters')]);
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

    #[Route('room-cards', name: 'room-card-data-v2', methods: [Request::METHOD_GET])]
    public function roomCards(Request $request, RoomRepositoryInterface $roomRepository): Response
    {
        $roomId = $request->get('room-id');

        if (!is_numeric($roomId)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }
        try {
            $room = $roomRepository->findOneById($roomId);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['An error occurred while retrieving device data']);
        }

        if (!$room instanceof Room) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Room')]);
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::VIEW_ROOM_CARD_DATA, $room);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $cardDatePreFilterDTO = $this->prepareFilters($request);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request not formatted correctly']);
        }

        $cardViewTypeFilter = new CardViewTypeFilterDTO($room);
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter,self::ROOM_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Card filters')]);
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

    #[Route('index', name: 'index-card-data-v2', methods: [Request::METHOD_GET])]
    public function indexCards(Request $request)
    {
        try {
            $cardDatePreFilterDTO = $this->prepareFilters($request);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request not formatted correctly']);
        }

        $cardViewTypeFilter = new CardViewTypeFilterDTO();
        try {
            $cardData = $this->prepareCardDataForUser($cardDatePreFilterDTO, $cardViewTypeFilter);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Card filters')]);
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

    /**
     * @throws ORMException|WrongUserTypeException
     */
    private function prepareCardDataForUser(
        CardDataPreFilterDTO $cardDataPreFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array
    {
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

    // /**
    //  * @throws ExceptionInterface
    //  */
    // private function normalizeResponse(array $cardDTOs): array
    // {
    //     $normaliser = [new ObjectNormalizer()];

    //     return (new Serializer($normaliser))->normalize($cardDTOs);
    // }
}
