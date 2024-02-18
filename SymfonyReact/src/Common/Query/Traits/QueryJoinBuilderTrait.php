<?php

namespace App\Common\Query\Traits;

use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;

trait QueryJoinBuilderTrait
{
    /**
     * @param JoinQueryDTO[] $joinConditionDTOs
     * @param QueryBuilder $qb
     * @return string
     */
    public function prepareSensorJoinsForQuery(array $joinConditionDTOs, QueryBuilder $qb): string
    {
        $alias = [];
        foreach ($joinConditionDTOs as $joinConditionDTO) {
            /** @var  $sensorNameJoinConditionString */
            $sensorNameJoinConditionString = $this->createJoinConditionString(
                $joinConditionDTO->getJoinConditionId(),
                $joinConditionDTO->getJoiningConditionId(),
                $joinConditionDTO->getJoiningConditionColumn()
            );

            $alias[] = $joinConditionDTO->getAlias();
            $qb->leftJoin($joinConditionDTO->getObject(), $joinConditionDTO->getAlias(), Join::WITH, $joinConditionDTO->getAlias().$sensorNameJoinConditionString);
        }

        return implode(', ', $alias);
    }

    #[Pure]
    public function createJoinConditionString(string $joinConditionId, string $joiningConditionId, string $joinConditionColumn): string
    {
        return sprintf(
            '.%s = %s.%s',
            $joinConditionId,
            $joinConditionColumn,
            $joiningConditionId
        );
    }
}
