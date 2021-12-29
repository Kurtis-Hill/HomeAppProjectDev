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
use App\UserInterface\Exceptions\FailedToFilterSensorTypesException;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Services\Cards\CardDataFilterService\CardDataFilterService;
use App\UserInterface\Services\Cards\CardPreparation\CardPreparationServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\ORMException;
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

    private CardPreparationServiceInterface $cardPreparationService;

    private Request $request;

    public function __construct(
        Request $request,
        CardDataFilterService $cardDataFilterService,
        CardPreparationServiceInterface $cardPreparationService,
    ) {
        $this->cardDataFilterService = $cardDataFilterService;
        $this->cardPreparationService = $cardPreparationService;
        $this->request = $request;
    }

    #[Route('device-cards', name: 'device-card-data-v2', methods: [Request::METHOD_GET])]
    public function deviceCards(DeviceRepositoryInterface $deviceRepository): Response
    {
        $deviceId = $this->request->get('device-id');

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

        $cards = $this->getCardsPreparedForUser();

        $responseData = $this->normalizeResponse($cards);

        return $this->sendSuccessfulResponse($responseData);
    }

    #[Route('room-cards', name: 'room-card-data-v2', methods: [Request::METHOD_GET])]
    public function roomCards(RoomRepositoryInterface $roomRepository): Response
    {
        $deviceId = $this->request->get('room-id');

        if (!is_numeric($deviceId)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }
        try {
            $room = $roomRepository->findOneById($deviceId);
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
            $cards = $this->getCardsPreparedForUser();
        } catch (WrongUserTypeException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $responseData = $this->normalizeResponse($cards);

        return $this->sendSuccessfulResponse($responseData);
    }
    // filters = [
    // sensorTypes => [int, 2, 3],
    // readingTypes => [string <Temperature, Humidity, etc>]
    //
    /**
     * @throws WrongUserTypeException
     */
    private function getCardsPreparedForUser(): array
    {
        $filters = $this->request->get('filters') ?? [];
        $view = $this->request->get('view');

        $cardDataFilterDTO = new CardDataPreFilterDTO(
            $filters['sensorTypes'] ?? [],
            $filters['readingTypes'] ?? [],
        );

        $postFilterCardDataToQuery = $this->cardDataFilterService->filterSensorTypes($cardDataFilterDTO);

        $cardData = $this->cardPreparationService->prepareCardsForUser($this->getUser(), $postFilterCardDataToQuery, $view);
    }


    private function normalizeResponse(array $cardDTOs): string
    {
        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $serializer->serialize($cardDTOs, 'json');
    }
}
