<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;

interface CardReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
