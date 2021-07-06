<?php


namespace App\HomeAppSensorCore\Interfaces\SensorTypes;


use App\Entity\Card\CardView;
use App\Entity\Sensors\Sensors;

Interface StandardSensorTypeInterface
{
    public function getSensorObject(): Sensors;

    public function getSensorTypeID(): int;

    public function setSensorTypeID(int $id): void;

    public function setSensorObject(Sensors $sensor);

    public function getSensorNameID(): Sensors;

    public function getCardViewObject(): ?CardView;

    public function setCardViewObject(CardView $cardView): void;
}
