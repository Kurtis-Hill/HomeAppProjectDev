<?php


namespace App\HomeAppSensorCore\Interfaces;


interface APIErrorInterface
{
    public function getServerErrors(): array;
    public function getUserInputErrors(): array;
}
