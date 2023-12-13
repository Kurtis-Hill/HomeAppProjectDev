<?php

namespace App\Sensors\Entity\ReadingTypes;

use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity(repositoryClass: BaseSensorReadingTypeRepository::class)]
#[ORM\Table(name: 'basereadingtype')]
class BaseSensorReadingType
{
    #[
        ORM\Column(name: 'baseReadingTypeID', type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $readingTypeID;

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }
}
