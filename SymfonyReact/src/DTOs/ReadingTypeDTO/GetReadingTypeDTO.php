<?php

namespace App\DTOs\ReadingTypeDTO;

///@TODO move this into esp sensor namespace
class GetReadingTypeDTO
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
