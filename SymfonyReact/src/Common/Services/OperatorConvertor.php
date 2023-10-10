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

        if ($value === 'false') {
            $value = false;
        }
        if ($value === 'true') {
            $value = true;
        }

        if (is_numeric($value)) {
            $value = (float) $value;
        }

        if ($valueThatTriggers === 'false') {
            $valueThatTriggers = false;
        }
        if ($valueThatTriggers === 'true') {
            $valueThatTriggers = true;
        }

        if (is_numeric($valueThatTriggers)) {
            $valueThatTriggers = (float) $valueThatTriggers;
        }
        return match ($operatorSymbol) {
            Operator::OPERATOR_EQUAL => $value === $valueThatTriggers,
            Operator::OPERATOR_NOT_EQUAL => $value !== $valueThatTriggers,
            Operator::OPERATOR_GREATER_THAN_OR_EQUAL => $value >= $valueThatTriggers,
            Operator::OPERATOR_LESS_THAN_OR_EQUAL => $value <= $valueThatTriggers,
            Operator::OPERATOR_GREATER_THAN => $value > $valueThatTriggers,
            Operator::OPERATOR_LESS_THAN => $value < $valueThatTriggers,
            default => throw new OperatorConvertionException(sprintf(OperatorConvertionException::MESSAGE, $operator)),
        };
    }
}
