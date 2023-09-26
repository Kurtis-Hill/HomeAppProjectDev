<?php

namespace App\Common\Services;

use App\Common\Entity\Operator;
use App\Common\Exceptions\OperatorConvertionException;

class OperatorConvertor
{
    /**
     * @throws OperatorConvertionException
     */
    public static function convertUsingOperator(
        Operator $operator,
        mixed $value,
        mixed $valueThatTriggers
    ): string {
        $operatorSymbol = $operator->getOperatorSymbol();
        if ($operatorSymbol === Operator::OPERATOR_EQUAL) {
            return $value === $valueThatTriggers;
        }

        if ($operatorSymbol === Operator::OPERATOR_NOT_EQUAL) {
            return $value !== $valueThatTriggers;
        }

        if (!is_bool($value)) {
            if ($operatorSymbol === Operator::OPERATOR_GREATER_THAN_OR_EQUAL) {
                return '>=';
            }

            if ($operatorSymbol === Operator::OPERATOR_LESS_THAN_OR_EQUAL) {
                return '<=';
            }

            if ($operatorSymbol === Operator::OPERATOR_GREATER_THAN) {
                return '>';
            }

            if ($operatorSymbol === Operator::OPERATOR_LESS_THAN) {
                return '<';
            }
        }

        throw new OperatorConvertionException(sprintf(OperatorConvertionException::MESSAGE, $operator));
    }
}
