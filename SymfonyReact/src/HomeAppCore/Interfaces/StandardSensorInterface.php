<?php

namespace App\HomeAppCore\Interfaces;



interface StandardSensorInterface
{
    public function getCurrentSensorReading();

    public function setCurrentSensorReading();

    public function getLowReading();

    public function setLowReading();

    public function getHighReading();

    public function setHighReading();

    public function getConstrecord();

    public function getTime();

    public function getGroupnameid();

    public function getRoomid();

    public function getCardviewid();

    public function getSensornameid();
}