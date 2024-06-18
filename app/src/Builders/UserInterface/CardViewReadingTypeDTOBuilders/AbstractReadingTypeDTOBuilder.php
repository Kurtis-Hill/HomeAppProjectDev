<?php

namespace App\Builders\UserInterface\CardViewReadingTypeDTOBuilders;

use DateTimeInterface;

abstract class AbstractReadingTypeDTOBuilder
{
    protected function formatDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('d-m-Y H:i:s');
    }
}
