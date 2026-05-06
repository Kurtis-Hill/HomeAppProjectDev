<?php


namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface AnalogReadingTypeInterface
{
    public function getMaxAnalog(): float|int;

    public function getMinAnalog(): float|int;
}
