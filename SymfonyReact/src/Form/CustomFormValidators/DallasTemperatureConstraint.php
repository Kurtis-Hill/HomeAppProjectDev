<?php


namespace App\Form\CustomFormValidators;


class DallasTemperatureConstraint
{
    public $minMessage = 'Temperature for this sensor cannot be under -55°C you entered "{{ string }}"';

    public $maxMessage = 'Temperature for this sensor cannot be over 125°C you entered "{{ string }}"';
}