<?php

namespace App\UserInterface\Builders\CardViewReadingTypeDTOBuilders;

use DateTimeInterface;

abstract class AbstractReadingTypeDTOBuilder
{
    protected function formatDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('d-m-Y H:i:s');
    }
}
