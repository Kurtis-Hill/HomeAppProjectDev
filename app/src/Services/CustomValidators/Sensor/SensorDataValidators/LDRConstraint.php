<?php

namespace App\Services\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\SensorTypes\LDR;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class LDRConstraint extends Constraint
{
    public string $maxMessage = 'Reading for ' . LDR::NAME . ' sensor cannot be over ' . LDR::HIGH_READING . ' you entered {{ string }}';

    public string $minMessage = 'Reading for ' . LDR::NAME . ' sensor cannot be under ' . LDR::LOW_READING . ' you entered {{ string }}';

    public string $intMessage = 'The submitted value is not a number {{ string }}';
}
