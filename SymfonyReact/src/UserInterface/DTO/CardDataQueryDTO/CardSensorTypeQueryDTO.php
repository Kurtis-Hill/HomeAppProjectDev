<?php

namespace App\UserInterface\DTO\CardDataQueryDTO;

class CardSensorTypeQueryDTO
{
    private string $alias;

    private string $object;

    private array $joinCondition;

    public function __construct(string $alias, string $object, array $joinCondition)
    {
        $this->alias = $alias;
        $this->object = $object;
        $this->joinCondition = $joinCondition;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getJoinCondition(): array
    {
        return $this->joinCondition;
    }
}
