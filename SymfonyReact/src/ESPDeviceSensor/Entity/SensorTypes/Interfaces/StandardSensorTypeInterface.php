<?php


namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\User\Entity\UserInterface\Card\CardView;
use App\ESPDeviceSensor\Entity\Sensor;

Interface StandardSensorTypeInterface
{
    public function getCardViewObject(): ?CardView;

    public function setCardViewObject(CardView $cardView): void;
}
