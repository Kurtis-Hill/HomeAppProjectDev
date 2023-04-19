<?php

namespace App\User\Exceptions\GroupExceptions;

use Exception;

class GroupNotFoundException extends Exception
{
    public const MESSAGE = 'Group name not found for id %d';
}
