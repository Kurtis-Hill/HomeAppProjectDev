<?php


interface StandardSensorInterface
{
    public function getCurrentSensorReading();

    public function setCurrentSensorReading();

    public function getLowSensorReading();

    public function setLowSensorReading();

    public function getHighSensorReading();

    public function setHighSensorReading();

    public function getConstrecord();

    public function getTime();

    public function getGroupnameid();

    public function getRoomid();

    public function getCardviewid();

    public function getSensornameid();
}