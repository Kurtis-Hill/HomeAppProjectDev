<?php

namespace App\UserInterface\Exceptions;

use Exception;

class CardFormTypeNotRecognisedException extends Exception
{
    public const MESSAGE = 'Icons form type not recognised';
}
