<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;

class NoSpecialCharactersContraint extends Constraint
{
    public $message = "The name cannot contain any special characters, please choose a diffrent name";
}