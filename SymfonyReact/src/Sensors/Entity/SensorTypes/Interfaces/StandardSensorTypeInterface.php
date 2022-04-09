<?php


namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\UserInterface\Entity\Card\CardView;

Interface StandardSensorTypeInterface
{
    public function getCardViewObject(): ?CardView;

    public function setCardViewObject(CardView $cardView): void;
}
