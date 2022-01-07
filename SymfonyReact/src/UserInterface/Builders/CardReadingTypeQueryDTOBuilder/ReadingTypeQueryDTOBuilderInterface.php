<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;

//@TODO move to espsensor name space
interface ReadingTypeQueryDTOBuilderInterface
{
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO;
}
