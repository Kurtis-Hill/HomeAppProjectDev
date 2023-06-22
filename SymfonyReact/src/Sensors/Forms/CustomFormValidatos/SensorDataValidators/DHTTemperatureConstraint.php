<?php

namespace App\Sensors\Forms\CustomFormValidatos\SensorDataValidators;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Dht;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DHTTemperatureConstraint extends Constraint
{
    public string $maxMessage = 'Temperature settings for Dht sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $minMessage = 'Temperature settings for Dht sensor cannot be below '. Dht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';

}
