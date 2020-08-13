<?php


namespace App\Controller\Sensors;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/HomeApp/new-sensor")
 */
class AddNewSensorController extends AbstractController
{
    /**
     * @Route("/form")
     */
    public function getFormForNewSensor()
    {

    }
}