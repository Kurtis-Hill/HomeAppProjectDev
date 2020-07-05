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

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        $icons = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
        $colours = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
        $states = $this->getDoctrine()->getRepository(Cardstate::class)->getAllStates();

        $cardFormData = ['cardSensorData' => $cardSensorData, 'icons' => $icons, 'colours' => $colours, 'states' => $states];


//         dd($cardSensorData);
//            if ($cardSensorData['t_tempid'] !== null ) {
//
//            }
//        }
//        else {
//            $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardviewid]);
//           // dd($cardSensorData[3] instanceof Temp);
//        }

//        if (($cardSensorData['t_tempid'] !== null && is_int((intval($cardSensorData['t_tempid']))) || $cardSensorData[3] instanceof Temp)) {
//            dd('heyyy');
//        }
//        dd('nooo');

//        $cardSensorData = ($request->isMethod('POST')) ?
//            $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]) :
//            $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardviewid]);
//        switch ()
        //$form = $this->createFormBuilder()


//        if ($cardSensorData['t_tempid'] && !$cardSensorData['h_humidid']) {
//          //  dd('temp');
//            $form = $this->createForm(TempHumidFormType::class, null, [
//                'sensorType' => 'Temp'
//            ]);
//        }
//        if ($cardSensorData['t_tempid'] && $cardSensorData['h_humidid']) {
//           // dd('tempHumid');
//
//            $form = $this->createForm(TempHumidFormType::class, null, [
//                'sensorType' => 'TempHumid'
//            ]);
//        }
//        if (($cardSensorData['a_analogid'])) {
//          //  dd('analog');
//            $form = $this->createForm(CardViewFormType::class, null, [
//                'sensorType' => 'Analog'
//            ]);
//        }

//
//        $form->handleRequest($request);
        //$states = $this->getDoctrine()->getRepository(Cardview::class)->getFormSelectData();
        //dd($states);


       // dd($cardSensorData);

//        if ($form->isSubmitted())
//        //{if ($form->isSubmitted() && $form->isValid())
//        {
//            dd($form->getData());
////            $cardView = $this->getDoctrine()->getRepository(Cardview::class)->findOneBy(['id' => $cardviewid]);
////
////            if ($cardView->getSensornameid()) {
////
////            }
//        }
//        else {
//            return new JsonResponse('error');
//        }

        return new JsonResponse($cardFormData);


    }

    /**
     * @Route("/updatecardview", name="updateCardView")
     */
    public function updateCardView(Request $request)
    {
        $cardviewid = $request->get('cardViewID');
        //dd((array)$request->getContent('Form Data'));
        //dd($request->request->all());        //$icon = $request->headers->get('content-type');
        //dd($cardviewid);
        //dd($icon);
        // $reading = $request->get('highReading');
        //dd($reading);
       // dd($request);
        //$formContent = $request->getContent('Form Data');
//        dd($formContent);
//        dd('he');
        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardviewid]);
        //  dd($request->getContent('Form Data'));
       //   dd($cardSensorData);
        if ($cardSensorData["temp"] instanceof Temp && !$cardSensorData["humid"] instanceof Humid) {
            $temp = new Temp();
            //  dd('temp');
            $form = $this->createForm(TempHumidFormType::class, null, [
                'sensorType' => $temp
            ]);
        }
        if ($cardSensorData["temp"] && $cardSensorData["humid"]) {
            // dd('tempHumid');

            $form = $this->createForm(CardViewFormType::class, null, [
                'sensorType' => 'TempHumid'
            ]);
        }
        if (($cardSensorData["analog"] instanceof Analog)) {

            $form = $this->createForm(CardViewFormType::class, null, [
                'sensorType' => 'Analog'
            ]);
        }

        $form->submit((array)$request->request->all());


        if ($form->isSubmitted())
            //{if ($form->isSubmitted() && $form->isValid())
        {
          //  dd($form->get(('highReading')));

           //dd($form->getData());
            dd($form->isValid());
//            $cardView = $this->getDoctrine()->getRepository(Cardview::class)->findOneBy(['id' => $cardviewid]);
//
//            if ($cardView->getSensornameid()) {
//
//            }
        }
        else {
            $errors = [];
            foreach ($form->getErrors() as $error) {
                $name = $error->getOrigin()->getName();
                $errors[$name] = $error->getMessage();
            }
            return new JsonResponse(['Name' => $name, 'errors' => $errors]);
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