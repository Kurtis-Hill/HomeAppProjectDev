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
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\Cards\CardDataFilterService\CardDataFilterService;
use App\UserInterface\Services\Cards\CardPreparation\CardViewPreparationServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-data/v2/')]
class CardController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    private CardDataFilterService $cardDataFilterService;

    private CardViewPreparationServiceInterface $cardPreparationService;

    private Request $request;

    public const ROOM_VIEW = 'room';

    public const DEVICE_VIEW = 'device';

    public function __construct(
//        Request $request,
        CardDataFilterService $cardDataFilterService,
        CardViewPreparationServiceInterface $cardPreparationService,
    ) {
        $this->cardDataFilterService = $cardDataFilterService;
        $this->cardPreparationService = $cardPreparationService;
//        $this->request = $request;
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
//        try {
            $cards = $this->preparedCardsForUser($cardDatePreFilterDTO, $cardViewTypeFilter, self::DEVICE_VIEW);
//        } catch (WrongUserTypeException) {
//            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
//        } catch (ORMException) {
//            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Card filters')]);
//        }

        $responseData = $this->normalizeResponse($cards);

        return $this->sendSuccessfulResponse($responseData);
    }

    #[Route('room-cards', name: 'room-card-data-v2', methods: [Request::METHOD_GET])]
    public function roomCards(Request $request, RoomRepositoryInterface $roomRepository): Response
    {
        $roomId = $this->request->get('room-id');

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
            $cards = $this->preparedCardsForUser($cardDatePreFilterDTO, $cardViewTypeFilter,self::ROOM_VIEW);
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, ' Card filters')]);
        }

        $responseData = $this->normalizeResponse($cards);

        return $this->sendSuccessfulResponse($responseData);
    }

    /**
     * @throws ORMException|WrongUserTypeException
     */
    private function preparedCardsForUser(
        CardDataPreFilterDTO $cardDataPreFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view,
    ): array
    {
        $postFilterCardDataToQuery = $this->cardDataFilterService->filterSensorsToQuery($cardDataPreFilterDTO);

        $cardData = $this->cardPreparationService->prepareCardsForUser(
            $this->getUser(),
            $postFilterCardDataToQuery,
            $cardViewTypeFilterDTO,
            $view
        );

        dd('card controller', $cardData);
    }

    private function prepareFilters(Request $request): CardDataPreFilterDTO
    {
        $filters = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return new CardDataPreFilterDTO(
            $filters['sensorTypes'] ?? [],
            $filters['readingTypes'] ?? [],
        );
    }

    private function normalizeResponse(array $cardDTOs): string
    {
        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $serializer->serialize($cardDTOs, 'json');
    }
}
