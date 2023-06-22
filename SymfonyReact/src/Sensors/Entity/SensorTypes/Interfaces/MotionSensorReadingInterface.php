<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\Sensor;

interface MotionSensorReadingInterface
{
    public function getSensor(): Sensor;

    public function setSensor(Sensor $sensor): void;

    public function getMotion(): Motion;

    public function setMotion(Motion $motion): void;
//    public function getCurrentReading(): bool;
    
//    public function setCurrentReading(bool $currentReading);
    
//    public function getRequestedReading(): bool;
//
//    public function setRequestedReading(bool $requestedReading): void;
//
//    public function getExpectedReading(): ?bool;
//
//    public function setExpectedReading(?bool $expectedReading): void;
//
//    public function getCreatedAt(): DateTimeInterface;
//
//    public function setCreatedAt(DateTimeInterface $createdAt): void;
//
//    public function getUpdatedAt(): DateTimeInterface;
//
//    public function setUpdatedAt(DateTimeInterface $updatedAt): void;
    
}
