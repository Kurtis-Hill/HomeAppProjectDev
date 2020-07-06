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
use App\Form\CardViewForms\TempHumidFormType;
use App\Form\CardViewFormType;
use App\Form\TempHumidCardFormType;
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
        else {
            //move update card view method content here when done
        }
    }

    /**
     * @Route("/updatecardview", name="updateCardView")
     */
    public function updateCardView(Request $request)
    {
        $cardviewid = $request->get('cardViewID');

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardviewid]);
        //dd($cardSensorData['cardView']->getSensornameid()->getSensortypeid()->getSensortype());


        $form = $this->createForm(TempHumidFormType::class, null, [
            'sensorType' => $cardSensorData['cardView']->getSensornameid()->getSensortypeid()->getSensortype()
            ]);

        //For Sensors with 2 reading types add here
        if ($cardSensorData['humid'] instanceof Humid) {
            $form->submit([
                'highReading' => $request->get('highReading'),
                'lowReading' => $request->get('lowReading'),
                'secondHighReading' => $request->get('secondHighReading'),
                'secondLowReading' => $request->get('secondLowReading'),
                'icon' => $request->get('icon'),
                'colour' => $request->get('cardColour'),
                'cardViewState' => $request->get('cardViewState'),
                'constRecord' => $request->get('constRecord'),
                'secondConstRecord' => $request->get('secondConstRecord')

            ]);
        }
        else {
            $form->submit([
                'highReading' => $request->get('highReading'),
                'lowReading' => $request->get('lowReading'),
                'icon' => $request->get('icon'),
                'colour' => $request->get('cardColour'),
                'cardViewState' => $request->get('cardViewState'),
                'constRecord' => $request->get('constRecord'),

            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();
            //dd($form->isValid());
        }
        else {
            $errors = [];
            foreach ($form->getErrors() as $error) {
                $name = $error->getOrigin()->getName();
                $errors[$name] = $error->getMessage();

                return new JsonResponse(['errors' => $errors]);
            }
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