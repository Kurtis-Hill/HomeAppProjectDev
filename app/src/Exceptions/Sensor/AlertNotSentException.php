<?php

namespace App\Exceptions\Sensor;

use Exception;

class AlertNotSentException extends Exception
{
    public function __construct(string $message = 'Alert not sent', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
