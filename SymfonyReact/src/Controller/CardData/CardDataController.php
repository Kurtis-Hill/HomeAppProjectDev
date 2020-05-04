<?php


namespace App\Controller\CardData;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Core\Sensornames;
use App\Form\CardViewFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CardDataController
 * @package App\Controller\CardData
 * @Route("/HomeApp/CardData")
 */
class CardDataController extends AbstractController
{
    /**
     * @Route("/cardviewform&id={cardviewid}", name="cardViewForm")
     */
    public function cardViewForm(Request $request, $cardviewid)
    {
        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        $cardView = new Cardview();

        $form = $this->createForm(CardViewFormType::class, $cardView, []);
        $errors = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            //$submittedData = $form->getData();
            foreach ($form->getErrors(true, true) as $error) {
                $propertyName = $error->getOrigin()->getName();
                $errors[$propertyName] = $error->getMessage();
            }
            return new JsonResponse();

        }

        return new JsonResponse($cardSensorData);

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