<?php

namespace App\Sensors\SensorServices\SensorTrigger;

use App\Common\Services\OperatorConvertor;
use App\Sensors\Entity\SensorTrigger;

class SensorTriggerChecker
{
    public function checkIfTriggered(SensorTrigger $sensorTrigger, mixed $value): bool
    {
        $value = SensorTriggerConvertor::convertMixedToString($value);

        $valueThatTriggers = SensorTriggerConvertor::convertStringToMixed($sensorTrigger->getValueThatTriggers());

//        $operator = OperatorConvertor::convertOperator($sensorTrigger->getOperator()->getOperator());
//
//        if ($operator === '=') {
//            return $value === $valueThatTriggers;
//        }
//
//        if ($operator === '<>') {
//            return $value !== $valueThatTriggers;
//        }
//
//        if ($operator === '>=') {
//            return $value >= $valueThatTriggers;
//        }
//
//        if ($operator === '<=') {
//            return $value <= $valueThatTriggers;
//        }
//
//        if ($operator === '>') {
//            return $value > $valueThatTriggers;
//        }
//
//        if ($operator === '<') {
//            return $value < $valueThatTriggers;
//        }

        throw new \Exception('Operator not found');
    }
}
