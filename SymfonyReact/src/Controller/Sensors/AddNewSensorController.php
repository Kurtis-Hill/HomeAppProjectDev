<?php


namespace App\Controller\Sensors;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/HomeApp/sensors/new-sensor")
 */
class AddNewSensorController extends AbstractController
{

    /**
     * @Route("/", name="new-sensor-landing-page")
     */
    public function showPage()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/form")
     */
    public function getFormForNewSensor()
    {

    }
}