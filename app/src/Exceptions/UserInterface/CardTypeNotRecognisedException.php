<?php

namespace App\Exceptions\UserInterface;

use Exception;

class CardTypeNotRecognisedException extends Exception
{
    public const CARD_TYPE_NOT_RECOGNISED = 'Icons type not recognised';
}
