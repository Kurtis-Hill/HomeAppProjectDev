<?php


namespace App\Form\CustomFormValidators;


use Symfony\Component\Validator\Constraint;

class NoSpecialCharactersConstraint extends Constraint
{
    public string $message = "The name cannot contain any special characters, please choose a different name";
}
