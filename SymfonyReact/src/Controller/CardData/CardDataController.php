<?php


namespace App\Controller\CardData;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Core\Sensornames;
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

        $form = $this->createForm(CardViewFormType::class);

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        $icons = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();


        $colours = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
        $states = $this->getDoctrine()->getRepository(Cardstate::class)->getAllStates();

        $cardFormData = ['cardSensorData' => $cardSensorData, 'icons' => $icons, 'colours' => $colours, 'states' => $states];

        if ($form->isSubmitted()) {
        dd($request);

            $sensorCardView = new Cardview();
            $sensorCardState = new Cardstate();

        }

        return new JsonResponse($cardFormData);

    }

    /**
     * @Route("/index", name="cardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnIndexAllCardData(Request $request, CardDataService $cardDataService)
    {
        $cardData = $cardDataService->returnAllCardSensorData('json', 'index');
        return new JsonResponse($cardData);
    }


    /**
     * @Route("/CardFomOptions", name="cardformdata")
     */
    public function getAllCardFormOptions()
    {
        $cardFormData['colours'] = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
        $cardFormData['icons'] = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
        $cardFormData['cardState'] = $this->getDoctrine()->getRepository(Cardstate::class)->getAllCardStates();

        return new JsonResponse($cardFormData);
    }

}