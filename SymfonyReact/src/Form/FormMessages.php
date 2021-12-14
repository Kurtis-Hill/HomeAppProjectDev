<?php

namespace App\Form;

class FormMessages
{
    public const FORM_PRE_PROCESS_FAILURE = 'Bad request somethings wrong with your request data, if the problem persists log out an back in again';

    public const MALFORMED_REQUEST_DATA = 'Malformed request please include: %s in your request of type %s';

    public const ACCESS_DENIED = 'You have been denied permission to perform this action';

    public const SHOULD_NOT_BE_BLANK = '%s name should not be blank';

    public const FORM_QUERY_ERROR = 'Query error please try again, if the issue continues log out and then back in again';

}
