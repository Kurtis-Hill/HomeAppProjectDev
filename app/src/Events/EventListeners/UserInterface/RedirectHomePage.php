<?php

namespace App\Events\EventListeners\UserInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectHomePage
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($throwable instanceof NotFoundHttpException) {
            $requestUri = $event->getRequest()->getRequestUri();
            if (str_contains($requestUri, 'HomeApp')) {
                $redirectResponse = new RedirectResponse('/HomeApp/WebApp/index');
                $event->setResponse($redirectResponse);
            }
        }
    }
}
