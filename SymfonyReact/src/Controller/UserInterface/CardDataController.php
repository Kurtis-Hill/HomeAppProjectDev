<?php


namespace App\Controller\UserInterface;

use App\API\Traits\HomeAppAPIResponseTrait;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateBoundaryReadingsInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use App\Form\CardViewForms\CardViewForm;
use App\Form\FormMessages;
use App\Services\CardUserDataService;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Voters\CardViewVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


#[Route('/HomeApp/api/card-data')]
class CardDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/cards', name: 'card-data', methods: [Request::METHOD_GET])]
    public function returnCardDataDTOs(Request $request, CardUserDataService $cardDataService): Response|JsonResponse
    {
        $route = $request->get('view');
        $deviceId = $request->get('device-id');

        $em = $this->getDoctrine()->getManager();

        if (isset($deviceId) && is_numeric($deviceId)) {
            $device = $em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $deviceId]);

            if ($device instanceof Devices) {
                try {
                    $this->denyAccessUnlessGranted(SensorVoter::VIEW_DEVICE_CARD_DATA, $device);
                } catch (AccessDeniedException $exception) {
                    return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
                }
            } else {
                return $this->sendBadRequestJsonResponse(['No device found']);
            }
        }

        $cardFilters = $request->get('filters') ?? [];
        $cardDataDTOs = $cardDataService->prepareCardDTOs($route, $device ?? null, $cardFilters);

        if (!empty($cardDataService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($cardDataService->getServerErrors());
        }
        if (!empty($cardDataService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($cardDataService->getUserInputErrors());
        }

        if (empty($cardDataDTOs)) {
            return $this->sendSuccessfulResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        $serializedCards = $serializer->serialize($cardDataDTOs, 'json');

        if (!empty($cardDataService->getCardErrors())) {
            return $this->sendPartialContentResponse($serializedCards);
        }

        return $this->sendSuccessfulResponse($serializedCards);
    }


    /**
     *
     * @param Request $request
     * @param CardUserDataService $cardDataService
     * @return Response|JsonResponse
     */
    #[Route('/card-sensor-form', name: 'card-view-form', methods: [Request::METHOD_GET])]
    public function showCardViewForm(Request $request, CardUserDataService $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->query->get('cardViewID');

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['malformed card view id request']);
        }

        $em = $this->getDoctrine()->getManager();

        $cardViewObject = $em->getRepository(CardView::class)->findOneBy(['cardViewID' => $cardViewID]);

        if ($cardViewObject instanceof CardView) {
            try {
                $this->denyAccessUnlessGranted(CardViewVoter::CAN_VIEW_CARD_VIEW_FORM, $cardViewObject);
            } catch (AccessDeniedException) {
                return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
            }
        } else {
            return $this->sendBadRequestJsonResponse(['Card view id not recognised']);
        }

        $cardFormDTO = $cardDataService->getCardViewFormDTO($cardViewObject);

        if ($cardFormDTO === null || !empty($cardDataService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($cardDataService->getServerErrors());
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($cardFormDTO, 'json'));
    }


    /**
     * @param Request $request
     * @param UpdateBoundaryReadingsInterface $updateBoundaryReadings
     * @param CardUserDataService $cardDataService
     * @return Response|JsonResponse
     */
    #[Route('/update-card-view', name: 'update-card-view', methods: [Request::METHOD_PUT])]
    public function updateCardView(
        Request $request,
        UpdateBoundaryReadingsInterface $updateBoundaryReadings,
        CardUserDataService $cardDataService
    ): Response|JsonResponse {
        try {
            $cardData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->sendBadRequestJsonResponse(['Format not expected']);
        }

        $cardViewID = $cardData['cardViewID'];

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['malformed card view id not recognised']);
        }

        $em = $this->getDoctrine()->getManager();

        $cardViewObject = $em->getRepository(CardView::class)->findOneBy(['cardViewID' => $cardViewID]);

        if ($cardViewObject instanceof CardView) {
            try {
                $this->denyAccessUnlessGranted(CardViewVoter::CAN_EDIT_CARD_VIEW_FORM, $cardViewObject);
            } catch (AccessDeniedException) {
                return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
            }
        } else {
            return $this->sendBadRequestJsonResponse(['card not found by the id given']);
        }

        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

        $cardViewData = [
            'cardColourID' => $cardData['cardColour'],
            'cardIconID' => $cardData['cardIcon'],
            'cardStateID' => $cardData['cardViewState'],
        ];

        $cardDataService->processForm($cardViewForm, $cardViewData);

        if ($cardDataService->getUserInputErrors()) {
            return $this->sendBadRequestJsonResponse($cardDataService->getUserInputErrors());
        }

        $em->persist($cardViewForm->getData());

        $sensorObject = $cardViewObject->getSensorNameID();
        $updateBoundaryReadings->handleSensorReadingBoundaryUpdate(
            $cardViewObject->getSensorNameID()->getDeviceObject(),
            $sensorObject->getSensorName(),
            $cardData
        );

        if (!empty($updateBoundaryReadings->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($updateBoundaryReadings->getUserInputErrors());
        }

        if (!empty($updateBoundaryReadings->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($updateBoundaryReadings->getServerErrors());
        }

        $em->flush();

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
