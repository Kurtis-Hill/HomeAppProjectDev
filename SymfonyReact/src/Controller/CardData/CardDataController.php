<?php


namespace App\Controller\CardData;

use App\Form\CardViewForms\CardViewForm;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use App\Services\CardDataServiceUser;
use App\Services\SensorData\SensorUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Entity\Sensors\SensorType;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Class CardDataController.
 *
 * @Route("/HomeApp/api/card-data")
 *
 */
class CardDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/cards", name="card-data", methods={"GET"})
     * @param Request $request
     * @param CardDataServiceUser $cardDataService
     * @return Response|JsonResponse
     */
    public function returnCardDataDTOs(Request $request, CardDataServiceUser $cardDataService): Response|JsonResponse
    {
        $cardData = $cardDataService->prepareAllCardDTOs($request);

        if (!empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse(['errors' => 'Something went wrong we are logging you out']);
        }

        if (empty($cardData)) {
            return $this->sendSuccessfulResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        if (!empty($cardDataService->getCardErrors())) {
            return $this->sendPartialContentResponse($serializer->serialize($cardData, 'json'));
        }

        return $this->sendSuccessfulResponse($serializer->serialize($cardData, 'json'));
    }


    /**
     * @Route("/card-state-view-form", name="cardViewForm", methods={"GET"})
     *
     * @param Request $request
     * @param CardDataServiceUser $cardDataService
     * @return Response|JsonResponse
     */
    public function showCardViewForm(Request $request, CardDataServiceUser $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->query->get('cardViewID');

        if (empty($cardViewID)) {
            return $this->sendBadRequestResponse();
        }

        $cardFormDTO = $cardDataService->getCardViewFormDTO($cardViewID);

        if ($cardFormDTO === null || !empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($cardFormDTO, 'json'));
    }


    /**
     * @Route("/update-card-view", name="updateCardView", methods={"POST"})
     * @param Request $request
     * @param SensorUserDataService $sensorDataService
     * @param CardDataServiceUser $cardDataService
     * @return Response|JsonResponse
     */
    public function updateCardView(Request $request, SensorUserDataService $sensorDataService, CardDataServiceUser $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->get('card-view-id');

        $cardColourID = $request->get('card-colour');
        $cardIconID = $request->get('card-icon');
        $cardStateID = $request->get('card-view-state');

        if (empty($cardColourID) || empty($cardIconID) || empty($cardStateID) || empty($cardViewID)) {
            return $this->sendBadRequestResponse(['errors' => 'empty form data']);
        }

        $cardViewData = [
            'cardColourID' => $cardColourID,
            'cardIconID' => $cardIconID,
            'cardStateID' => $cardStateID,
        ];

        $cardSensorReadingObject = $cardDataService->editSelectedCardData($cardViewID);
        $cardViewObject = array_shift($cardSensorReadingObject);

        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

//        $sensorDataService->processForm($cardViewForm, $cardViewData);

        $cardViewForm->submit($cardViewData);

        if ($cardViewForm->isSubmitted() && $cardViewForm->isValid()) {
            $validFormData = $cardViewForm->getData();
            try {
                $this->getDoctrine()->getManager()->persist($validFormData);
            } catch (ORMException | \Exception $e) {
                return $this->sendBadRequestResponse();
            }
        }
        //        $sensorDataService->processForm($cardViewForm, $this->getDoctrine()->getManager(), $cardViewData);
//        if (!empty($sensorDataService->returnAllFormInputErrors())) {
//            return $this->sendBadRequestResponse($sensorDataService->returnAllFormInputErrors());
//        }

        $sensorTypeObject = $cardViewObject->getSensorNameID();

        $sensorDataService->handleSensorReadingBoundary($request, $sensorTypeObject, $cardSensorReadingObject);

        if (!empty($sensorDataService->getUserInputErrors())) {
            return $this->sendBadRequestResponse($sensorDataService->getUserInputErrors());
        }

//        if (!empty($sensorDataService->returnAllFormInputErrors())) {
//            return $this->sendBadRequestResponse($sensorDataService->returnAllFormInputErrors());
//        }

        if (!empty($sensorDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse();
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->sendSuccessfulUpdateJsonResponse();
    }

}
