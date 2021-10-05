<?php

namespace App\ESPDeviceSensor\Forms\CustomFormValidatos\SensorDataValidators;

use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use Symfony\Component\Validator\Constraint;

class DHTTemperatureConstraint extends Constraint
{
    public string $maxMessage = 'Temperature settings for Dht sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $minMessage = 'Temperature settings for Dht sensor cannot be below '. Dht::LOW_TEMPERATURE_READING_BOUNDRY . Temperature::READING_SYMBOL . ' you entered {{ string }}'. Temperature::READING_SYMBOL;

    public string $intMessage = 'The submitted value is not a number "{{ string }}"';

}
