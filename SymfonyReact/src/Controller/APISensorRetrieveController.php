<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class APISensorRetrieveController extends AbstractController
{
    /**
     * @Route("HomeApp/api/retrieve/tempsensor&groupID={id}", name="a_p_i_sensor_retrieve")
     */
    public function index($id)
    {
        return $this->render('api_sensor_retrieve/index.html.twig', [
            'controller_name' => 'APISensorRetrieveController',
        ]);
    }
}
