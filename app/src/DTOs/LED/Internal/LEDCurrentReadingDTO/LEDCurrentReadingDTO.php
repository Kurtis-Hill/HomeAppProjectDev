<?php

namespace App\DTOs\LED\Internal\LEDCurrentReadingDTO;

class LEDCurrentReadingDTO
{
    public function __construct(
        private int $red,
        private int $green,
        private int $blue
    ) {
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }
}
