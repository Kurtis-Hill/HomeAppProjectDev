<?php

namespace App\Common\DTO\Response;

readonly class OperatorResponseDTO
{
    public function __construct(
        private int $operatorID,
        private string $operatorName,
        private string $operatorSymbol,
        private string $operatorDescription,
    ) {
    }
}
