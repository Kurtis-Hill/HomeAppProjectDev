<?php

namespace App\UserInterface\Exceptions;

use Exception;

class CardTypeNotRecognisedException extends Exception
{
    public const CARD_TYPE_NOT_RECOGNISED = 'Card type not recognised';
}
