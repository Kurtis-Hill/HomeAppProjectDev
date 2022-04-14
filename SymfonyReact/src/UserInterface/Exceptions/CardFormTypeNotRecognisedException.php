<?php

namespace App\UserInterface\Exceptions;

use Exception;

class CardFormTypeNotRecognisedException extends Exception
{
    public const MESSAGE = 'Card form type not recognised';
}
