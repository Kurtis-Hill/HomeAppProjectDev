<?php

namespace App\UserInterface\Exceptions;

use Exception;

class CardStateException extends Exception
{
    public const CARD_STATE_NOT_FOUND = 'Icons state not found';
}
