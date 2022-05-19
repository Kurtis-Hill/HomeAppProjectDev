<?php

namespace App\UserInterface\DTO\CardDataFiltersDTO;

class CardFilteredReadingTypeDTO
{
    private string $readingTypeObject;

    private string $alias;

    public function __construct(string $readingTypeObject, string $alias)
    {
        $this->readingTypeObject = $readingTypeObject;
        $this->alias = $alias;
    }

    public function getReadingTypeObject(): string
    {
        return $this->readingTypeObject;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
