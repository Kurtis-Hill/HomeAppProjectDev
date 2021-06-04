<?php


namespace App\Form;


class FormMessages
{
    public const FORM_PRE_PROCESS_FAILURE = 'Bad request somethings wrong with your form data, if the problem persists log out an back in again';

    public const FORM_PROCESS_FAILURE_MESSAGE = 'You have been denied permission to perform this action';

    public const SHOULD_NOT_BE_BLANK = '%s name should not be blank';
}
