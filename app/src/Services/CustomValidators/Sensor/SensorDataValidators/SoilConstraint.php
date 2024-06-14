<?php

namespace App\Services\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\SensorTypes\Soil;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SoilConstraint extends Constraint
{
    public string $maxMessage = 'Reading for ' . Soil::NAME . ' sensor cannot be over ' . Soil::HIGH_SOIL_READING_BOUNDARY . ' you entered {{ string }}';

    public string $minMessage = 'Reading for ' . Soil::NAME . ' sensor cannot be under ' . Soil::LOW_SOIL_READING_BOUNDARY . ' you entered {{ string }}';

    public string $intMessage = 'The submitted value is not a number {{ string }}';
}
