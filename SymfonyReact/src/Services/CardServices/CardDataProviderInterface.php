<?php

namespace App\Services\CardServices;

interface CardDataProviderInterface
{
    public function prepareCardDTOs(): array;
}
