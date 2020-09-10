<?php


namespace App\Controller\Core;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/devices")
 */
class AddNewDevice extends AbstractController
{
    /**
     * @Route("/new-device", name="new-device-landing-page")
     */
    public function showPage()
    {
        return $this->render('index/index.html.twig');
    }

}