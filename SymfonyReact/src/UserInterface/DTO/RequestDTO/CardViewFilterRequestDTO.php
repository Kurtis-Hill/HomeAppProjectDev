<?php

namespace App\UserInterface\DTO\RequestDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CardViewFilterRequestDTO
{
    //These are the sensor types and reading types not to be queried

    #[Assert\Type(type: ['array', "null"], message: 'sensorTypes must be an {{ type }} you have provided {{ value }}')]
    private mixed $sensorTypes = [];

    #[Assert\Type(type: ['array', "null"], message: 'readingTypes must be an {{ type }} you have provided {{ value }}')]
    private mixed $readingTypes = [];

    public function getSensorTypes(): mixed
    {
        return $this->sensorTypes;
    }

    public function setSensorTypes(mixed $sensorTypes): void
    {
        $this->sensorTypes = $sensorTypes;
    }

    public function getReadingTypes(): mixed
    {
        return $this->readingTypes;
    }

    public function setReadingTypes(mixed $readingTypes): void
    {
        $this->readingTypes = $readingTypes;
    }
}
