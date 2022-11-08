<?php

namespace App\Common\Query\Traits;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;

trait QueryJoinBuilderTrait
{
    public function prepareSensorJoinsForQuery(array $joinConditionDTO, QueryBuilder $qb): string
    {
        $alias = [];
        foreach ($joinConditionDTO as $cardSensorTypeQueryDTO) {
            /** @var  $sensorNameJoinConditionString */
            $sensorNameJoinConditionString = $this->createJoinConditionString(
                $cardSensorTypeQueryDTO->getJoinConditionId(),
                $cardSensorTypeQueryDTO->getJoinConditionColumn()
            );

            $alias[] = $cardSensorTypeQueryDTO->getAlias();
            $qb->leftJoin($cardSensorTypeQueryDTO->getObject(), $cardSensorTypeQueryDTO->getAlias(), Join::WITH, $cardSensorTypeQueryDTO->getAlias().$sensorNameJoinConditionString);
        }

        return implode(', ', $alias);
    }

    #[Pure]
    public function createJoinConditionString(string $joinConditionId, string $joinConditionColumn): string
    {
        return sprintf(
            '.%s = %s.%s',
            $joinConditionId,
            $joinConditionColumn,
            $joinConditionId
        );
    }
}
