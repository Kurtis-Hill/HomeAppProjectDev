<?php

namespace App\UserInterface\Services\Cards\CardCreation;

use App\Sensors\Entity\Sensor;
use Symfony\Component\Security\Core\User\UserInterface;

interface CardCreationServiceInterface
{
    public function createUserCardForSensor(Sensor $sensorObject, UserInterface $user): array;
}
