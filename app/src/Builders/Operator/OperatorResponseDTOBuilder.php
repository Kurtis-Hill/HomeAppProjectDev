<?php

namespace App\Builders\Operator;

use App\DTOs\Operator\Response\OperatorResponseDTO;
use App\Entity\Common\Operator;

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
