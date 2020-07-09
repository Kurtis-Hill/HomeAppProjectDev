<?php


namespace App\Controller\CardData;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Core\Sensornames;
use App\Entity\Sensors\Analog;
use App\Entity\Sensors\Humid;
use App\Entity\Sensors\Temp;
use App\Form\CardViewForms\AnalogFormType;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\DHTTempHumidCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\Form\CardViewForms\TempHumidFormType;
use App\Form\CardViewFormType;
use App\Form\CardViewModalFormType;
use App\Form\TempHumidCardFormType;
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
        } else {
            //move update card view method content here when done
        }
    }

    /**
     * @Route("/updatecardview", name="updateCardView")
     */
    public function updateCardView(Request $request)
    {
        //dd($request->request->all());
        $errors = [];
        $cardViewID = $request->get('cardViewID');

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewID]);

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
            $formData = [];
            $form = null;
            $em = $this->getDoctrine()->getManager();
            if ($sensorType === 'DHT') {
                $temp = $this->getDoctrine()->getRepository(Temp::class)->findOneBy(['cardviewid' => $cardViewID]);
                $humid = $this->getDoctrine()->getRepository(Humid::class)->findOneBy(['cardviewid' => $cardViewID]);

                $form = $this->createForm(DHTTempCardModalForm::class, $temp);
                $formData = [
                    'hightemp' => $request->get('highReading'),
                    'lowtemp' => $request->get('lowReading'),
                    'constrecord' => $request->get('constRecord'),
                ];
                $secondForm = $this->createForm(DHTHumidCardModalForm::class, $humid);
                $secondFormData = [
                    'highhumid' => $request->get('secondHighReading'),
                    'lowhumid' => $request->get('secondLowReading'),
                    'humidconstrecord' => $request->get('secondConstRecord')
                ];
                //$em->persist();
            }

            if ($sensorType === "Dallas Temperature") {
                $form = $this->createForm(TempHumidFormType::class, null, [
                    'sensorType' => $sensorType
                ]);
                $formData = [
                    'highReading' => $request->get('highReading'),
                    'lowReading' => $request->get('lowReading'),
                    'constRecord' => $request->get('constRecord')
                ];
                $temp = $this->getDoctrine()->getRepository(TempRepository::class)->findOneBy(['cardviewid' => $cardViewID]);
            }

            if ($sensorType === "Soil") {
                $form = $this->createForm(SoilFormType::class, null, [
                    'sensorType' => $sensorType
                ]);
                $formData = [
                    'highReading' => $request->get('highReading'),
                    'lowReading' => $request->get('lowReading'),
                    'constRecord' => $request->get('constRecord')
                ];
                $soil = $this->getDoctrine()->getRepository(AnalogRepository::class)->findOneBy(['cardviewid' => $cardViewID]);
            }

            if ($form !== null) {
                $form->submit($formData);

                if ($form->isSubmitted() && $form->isValid()) {
                    $validFormData = $form->getData();
                    dd('it worked');

                    if (!$secondForm !== null) {
                        $secondForm->submit($secondFormData);

                        if ($secondForm->isSubmitted() && $secondForm->isValid()) {

                        }

                        else {
                            foreach ($secondForm->getErrors() as $error) {
                                $name = $error->getOrigin()->getName();
                                $errors[$name] = $error->getMessage();
                            }
                        }
                    }

                    else {
                        $errors['Form'] = "Failed to Prepare Second Form";
                    }
                }

                else {
                    foreach ($form->getErrors() as $error) {
                        $name = $error->getOrigin()->getName();
                        $errors[$name] = $error->getMessage();
                        //ADD HTTP RESPONSE CODE
                    }
                    //dd($form->getErrors());
                }
            }
            else {
                $errors['Form'] = "Form Failed To Prepare";
            }

        }

        return new JsonResponse(['errors' => $errors], 500);
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


//    /**
//     * @Route("/CardFomOptions", name="cardformdata")
//     */
//    public function getAllCardFormOptions()
//    {
//        $cardFormData['colours'] = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
//        $cardFormData['icons'] = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
//        $cardFormData['cardState'] = $this->getDoctrine()->getRepository(Cardstate::class)->getAllCardStates();
//
//        return new JsonResponse($cardFormData);
//    }

}