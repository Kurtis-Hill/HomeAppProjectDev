<?php

namespace App\CustomValidators\Sensor\SensorDataValidators;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class LatitudeConstraint extends Constraint
{
    public string $maxMessage = 'The highest possible latitude is 90' . Latitude::READING_SYMBOL . ' you entered {{ string }}' . Latitude::READING_SYMBOL;

    public string $minMessage = 'The lowest possible latitude is -90' . Latitude::READING_SYMBOL . ' you entered {{ string }}' . Latitude::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number {{ string }}';
}
