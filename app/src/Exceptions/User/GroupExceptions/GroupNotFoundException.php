<?php

namespace App\Exceptions\User\GroupExceptions;

use Exception;

class GroupNotFoundException extends Exception
{
    public const MESSAGE = 'Group not found for id %d';
}
