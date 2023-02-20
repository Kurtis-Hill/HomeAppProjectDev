<?php

namespace App\Common\Query\Traits;

use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;

trait QueryJoinBuilderTrait
{
    /**
     * @param JoinQueryDTO[] $joinConditionDTO
     * @param QueryBuilder $qb
     * @return string
     */
    public function prepareSensorJoinsForQuery(array $joinConditionDTO, QueryBuilder $qb): string
    {
        $alias = [];
        foreach ($joinConditionDTO as $cardSensorTypeQueryDTO) {
            /** @var  $sensorNameJoinConditionString */
            $sensorNameJoinConditionString = $this->createJoinConditionString(
                $cardSensorTypeQueryDTO->getJoinConditionId(),
                $cardSensorTypeQueryDTO->getJoiningConditionId(),
                $cardSensorTypeQueryDTO->getJoinConditionColumn()
            );

            $alias[] = $cardSensorTypeQueryDTO->getAlias();
            $qb->leftJoin($cardSensorTypeQueryDTO->getObject(), $cardSensorTypeQueryDTO->getAlias(), Join::WITH, $cardSensorTypeQueryDTO->getAlias().$sensorNameJoinConditionString);
        }

        return implode(', ', $alias);
    }

    #[Pure]
    public function createJoinConditionString(string $joinConditionId, string $joiningConditionId, string $joinConditionColumn): string
    {
//        dd($joinConditionId, $joinConditionColumn);
        return sprintf(
            '.%s = %s.%s',
            $joinConditionId,
            $joinConditionColumn,
            $joiningConditionId
        );
    }
}
