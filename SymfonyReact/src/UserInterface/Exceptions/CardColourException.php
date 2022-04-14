<?php

namespace App\UserInterface\Exceptions;

use Exception;

class CardColourException extends Exception
{
    public const FAILED_SETTING_RANDOM_COLOUR = 'Failed setting random colour';
}
