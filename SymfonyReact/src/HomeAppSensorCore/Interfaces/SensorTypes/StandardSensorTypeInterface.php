<?php


namespace App\HomeAppSensorCore\Interfaces\SensorTypes;


use App\Entity\Card\CardView;

Interface StandardSensorTypeInterface
{
    public function getCardViewObject(): CardView;

    public function getSensorTypeID(): int;


    public function setSensorTypeID(int $id): void;

    public function setCardViewObject(CardView $cardView);
}
