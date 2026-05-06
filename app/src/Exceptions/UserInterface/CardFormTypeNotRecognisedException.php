<?php

namespace App\Exceptions\UserInterface;

use Exception;

class CardFormTypeNotRecognisedException extends Exception
{
    public const MESSAGE = 'Icons form type not recognised';
}
