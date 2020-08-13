<?php


namespace App\Controller;


use App\PageData\LiveSensorData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/HomeApp")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) :Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    /**
     * @Route("/sensors/new-sensor", name="addNewSensor")
     * @param Request $request
     * @return Response
     */
    public function addNewSensorAction(Request $request) :Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }



}