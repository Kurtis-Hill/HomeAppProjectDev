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
use App\Repository\Sensors\AnalogRepository;
use App\Repository\Sensors\HumidRepository;
use App\Repository\Sensors\TempRepository;
use App\Services\CardDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CardDataController
 * @package App\Controller\CardData
 * @Route("/HomeApp/api/CardData")
 */
class CardDataController extends AbstractController
{
    /**
     * @Route("/cardviewform&id={cardviewid}", name="cardViewForm")
     */
    public function cardViewForm(Request $request, $cardviewid)
    {
        if ($request->isMethod('GET')) {
            $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

            $icons = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
            $colours = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
            $states = $this->getDoctrine()->getRepository(Cardstate::class)->getAllStates();

            $cardFormData = ['cardSensorData' => $cardSensorData, 'icons' => $icons, 'colours' => $colours, 'states' => $states];

            return new JsonResponse($cardFormData);
        }
    }

    /**
     * @Route("/updatecardview", name="updateCardView")
     */
    public function updateCardView(Request $request, CardDataService $cardDataService)
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
                //dd($processedForm);
                if ($processedForm !== false) {
                    $errors[] = $processedForm;
                }

                if (isset($secondForm)) {
                    $secondProcessedForm = $cardDataService->processForm($secondForm, $secondFormData);
                    if ($secondProcessedForm !== false) {
                        $errors[] = $secondProcessedForm;
                    }
                }
            }
            else {
                $errors['SystemFailed'] = "Form Failed To Prepare";
            }
        }

        else {
            $errors['SystemFailed'] = "CardView Form Not Valid";
        }

        if (empty($errors)) {
            try {
                $em->flush();
            } catch (\Exception $e) {
                $e->getMessage();
            }
            return new JsonResponse('sucess', 200);
        }
        else {
          //  dd($errors);
            return new JsonResponse(['errors' => $errors], 400);
        }
    }

    /**
     * @Route("/index", name="cardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnIndexAllCardData(Request $request, CardDataService $cardDataService)
    {
        $cardData = $cardDataService->returnAllCardSensorData('JSON', 'index');
        //dd($cardData);
        return new JsonResponse($cardData);
    }

}