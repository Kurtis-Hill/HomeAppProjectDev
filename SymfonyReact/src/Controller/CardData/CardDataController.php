<?php


namespace App\Controller\CardData;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Form\CardViewForms\AnalogFormType;
use App\Form\CardViewForms\CardViewModalFormType;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\DHTTempHumidCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\Form\CardViewForms\TempHumidFormType;

use App\Services\CardDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Class CardDataController
 * @package App\Controller\CardData
 * @Route("/HomeApp/api/CardData")
 */
class CardDataController extends AbstractController
{
    /**
     * @Route("/index", name="cardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnIndexAllCardData(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $cardData = $cardDataService->returnAllCardSensorData('JSON', 'index');

        if (!$cardData) {
            return new JsonResponse(['errors' => 'No card data found query error please logout and back in again please'], 400);
        }

        return new JsonResponse($cardData);
    }

    /**
     * @Route("/cardviewform&id={cardviewid}", name="cardViewForm")
     */
    public function showCardViewForm(Request $request, $cardviewid): JsonResponse
    {
        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        $icons = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
        $colours = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
        $states = $this->getDoctrine()->getRepository(Cardstate::class)->getAllStates();

        $cardFormData = ['cardSensorData' => $cardSensorData, 'icons' => $icons, 'colours' => $colours, 'states' => $states];

        return new JsonResponse($cardFormData);
    }

    /**
     * @Route("/updatecardview", name="updateCardView")
     */
    public function updateCardView(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $errors = [];
        $cardViewID = $request->get('cardViewID');

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewID, 'userID' =>  $cardDataService->getUserID()]);

        if (!$cardSensorData) {
            return new JsonResponse('No Sensor Found', 404);
        }

        $sensorType = $cardSensorData['cardView']->getSensornameid()->getSensortypeid()->getSensortype();

        $cardViewForm = $this->createForm(CardViewModalFormType::class, $cardSensorData['cardView']);

        $cardViewForm->submit([
                'cardcolourid' => $request->get('cardColour'),
                'cardiconid' => $request->get('icon'),
                'cardstateid' => $request->get('cardViewState'),
                ]
        );

        if ($cardViewForm->isSubmitted() && $cardViewForm->isValid()) {
            $form = null;
            $em = $this->getDoctrine()->getManager();

            if ($sensorType === 'DHT') {
                $form = $this->createForm(DHTTempCardModalForm::class, $cardSensorData['temp']);
                $formData = [
                    'hightemp' => $request->get('tempHighReading'),
                    'lowtemp' => $request->get('tempLowReading'),
                    'constrecord' => $request->get('constRecord'),
                ];

                $secondForm = $this->createForm(DHTHumidCardModalForm::class, $cardSensorData['humid']);
                $secondFormData = [
                    'highhumid' => $request->get('humidHighReading'),
                    'lowhumid' => $request->get('humidLowReading'),
                    'constrecord' => $request->get('secondConstRecord')
                ];
            }

            if ($sensorType === "Dallas Temperature") {
                $form = $this->createForm(DallasTempCardModalForm::class, $cardSensorData['temp']);
                $formData = [
                    'hightemp' => $request->get('tempHighReading'),
                    'lowtemp' => $request->get('tempLowReading'),
                    'constrecord' => $request->get('constRecord')
                ];
            }

            if ($sensorType === "Soil") {
                $form = $this->createForm(SoilFormType::class, $cardSensorData['analog']);
                $formData = [
                    'highanalog' => $request->get('analogHighReading'),
                    'lowanalog' => $request->get('analogLowReading'),
                    'constrecord' => $request->get('constRecord')
                ];
            }

            if ($form !== null) {
                $processedForm = $cardDataService->processForm($form, $formData);
                if ($processedForm instanceof FormInterface) {
                    foreach ($processedForm->getErrors(true, true) as $value) {
                        array_push($errors, $value->getMessage());
                    }
                }

                if (isset($secondForm)) {
                    $secondProcessedForm = $cardDataService->processForm($secondForm, $secondFormData);
                    if ($secondProcessedForm instanceof FormInterface) {
                        foreach ($secondProcessedForm->getErrors(true, true) as $value) {
                            array_push($errors, $value->getMessage());
                        }
                    }
                }
            }
            else {
                return new JsonResponse($errors[] = 'Sensor Not Recognised', 500);
            }
        }

        else {
            $errors[] = "CardView Form Not Valid";
        }

        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], 400);
        }
        else {
            try {
                $em->flush();
            } catch (\Exception $e) {
                $e->getMessage();
            }

            return new JsonResponse('sucess', 200);
        }
    }
}