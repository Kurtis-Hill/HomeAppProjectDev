<?php

namespace App\UserInterface\DTO\Internal\CardDataQueryDTO;

class JoinQueryDTO
{
    private string $alias;

    private string $object;

    private string $joinConditionId;

    private string $joiningConditionId;

    private string $joinConditionColumn;

    public function __construct(
        string $alias,
        string $object,
        string $joinConditionId,
        string $joiningConditionId,
        string $joiningConditionColumn
    ) {
        $this->alias = $alias;
        $this->object = $object;
        $this->joinConditionId = $joinConditionId;
        $this->joiningConditionId = $joiningConditionId;
        $this->joinConditionColumn = $joiningConditionColumn;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getJoinConditionId(): string
    {
        return $this->joinConditionId;
    }

    public function getJoiningConditionId(): string
    {
        return $this->joiningConditionId;
    }

    public function getJoiningConditionColumn(): string
    {
        return $this->joinConditionColumn;
    }
}
