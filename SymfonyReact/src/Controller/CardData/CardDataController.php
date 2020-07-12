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
       //dd($request->request->all());
        $errors = [];
        $cardViewID = $request->get('cardViewID');

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewID, 'userID' =>  $cardDataService->getUserID()]);
        //dd('yeu', $cardSensorData);
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
                $form = $this->createForm(DHTTempCardModalForm::class, $cardSensorData['temp']);
                $formData = [
                    'hightemp' => $request->get('highReading'),
                    'lowtemp' => $request->get('lowReading'),
                    'constrecord' => $request->get('constRecord'),
                ];

                $secondForm = $this->createForm(DHTHumidCardModalForm::class, $cardSensorData['humid']);
                $secondFormData = [
                    'highhumid' => $request->get('secondHighReading'),
                    'lowhumid' => $request->get('secondLowReading'),
                    'constrecord' => $request->get('secondConstRecord')
                ];
            }

            if ($sensorType === "Dallas Temperature") {
                $form = $this->createForm(DallasTempCardModalForm::class, null, [
                    'sensorType' => $sensorType
                ]);
                $formData = [
                    'highReading' => $request->get('highReading'),
                    'lowReading' => $request->get('lowReading'),
                    'constRecord' => $request->get('constRecord')
                ];
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
            }

            if ($form !== null) {
                $form->submit($formData);

                if ($form->isSubmitted() && $form->isValid()) {
                    $validFormData = $form->getData();
                    $em->persist($validFormData);

                    if ($secondForm) {
                        $secondForm->submit($secondFormData);

                        if ($secondForm->isSubmitted() && $secondForm->isValid()) {
                            $secondValidFormData = $secondForm->getData();
                            $em->persist($secondValidFormData);
                        }

                        else {
                            foreach ($secondForm->getErrors() as $error) {
                                $name = $error->getOrigin()->getName();
                                $errors[$name] = $error->getMessage();
                            }
                        }
                    }
                    try {
                        $em->flush();
                    }
                    catch (\Exception $e) {
                        $e->getMessage();
                    }
                    return new JsonResponse('Success', 200);
                }

                else {
                    foreach ($form->getErrors() as $error) {
                        $name = $error->getOrigin()->getName();
                        $errors[$name] = $error->getMessage();
                        //ADD HTTP RESPONSE CODE
                    }
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