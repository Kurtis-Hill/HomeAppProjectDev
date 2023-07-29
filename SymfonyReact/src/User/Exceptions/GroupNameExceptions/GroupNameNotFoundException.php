<?php

namespace App\User\Exceptions\GroupNameExceptions;

use Exception;

class GroupNameNotFoundException extends Exception
{
    public const MESSAGE = 'Group name not found for id %d';
}
