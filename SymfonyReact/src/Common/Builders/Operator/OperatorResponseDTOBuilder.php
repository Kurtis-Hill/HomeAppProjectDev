<?php

namespace App\Common\Builders\Operator;

use App\Common\DTO\Response\OperatorResponseDTO;
use App\Common\Entity\Operator;

class OperatorResponseDTOBuilder
{
    public static function buildOperatorResponseDTO(
        Operator $operator,
    ): OperatorResponseDTO {
        return new OperatorResponseDTO(
            operatorID: $operator->getOperatorID(),
            operatorName: $operator->getOperatorName(),
            operatorSymbol: $operator->getOperatorSymbol(),
            operatorDescription: $operator->getOperatorDescription(),
        );
    }
}
