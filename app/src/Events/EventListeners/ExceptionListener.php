<?php

namespace App\Events\EventListeners;

use App\Services\API\APIErrorMessages;
use App\Traits\HomeAppAPITrait;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    use HomeAppAPITrait;

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HttpException) {
            return;
        }

        $previous = $exception->getPrevious();
        if (!$previous instanceof ValidationFailedException) {
            return;
        }

        $errorMessages = [];
        foreach ($previous->getViolations() as $key => $violation) {
            $errorMessages[$violation->getPropertyPath() === "" ? $key : $violation->getPropertyPath()] = $violation->getMessage();
        }

        $response = $this->sendBadRequestJsonResponse($errorMessages, APIErrorMessages::VALIDATION_ERRORS);
        $event->setResponse($response);
    }
}
