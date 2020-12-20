<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/HomeApp/WebApp")
 *
 * whole app needs to handle exceptions and all the status codes properly, just getting the app on its feat first
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/{route}", name="index")
     *
     * @param $route
     */
    public function indexAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager, $route): Response
    {
        $one = 1;
        $two2 = 2;

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $token = $csrfTokenManager->getToken('authenticate')->getValue();

        return $this->render('index/index.html.twig', ['csrfToken' => $token]);
    }
}
