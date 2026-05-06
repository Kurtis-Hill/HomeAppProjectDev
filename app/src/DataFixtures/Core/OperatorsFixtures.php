<?php

namespace App\DataFixtures\Core;

use App\Entity\Common\Operator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OperatorsFixtures extends Fixture
{
    public const EQUALS = 'Equals';

    public const NOT_EQUALS = 'Not Equals';

    public const GREATER_THAN = 'Greater Than';

    public const LESS_THAN = 'Less Than';

    public const GREATER_THAN_OR_EQUAL_TO = 'Greater Than or Equal To';

    public const LESS_THAN_OR_EQUAL_TO = 'Less Than or Equal To';

    public const OPERATORS = [
        self::EQUALS => [
            'operatorName' => self::EQUALS,
            'operatorSymbol' => '==',
            'operatorDescription' => self::EQUALS,
        ],
        self::NOT_EQUALS => [
            'operatorName' => self::NOT_EQUALS,
            'operatorSymbol' => '!=',
            'operatorDescription' => self::NOT_EQUALS,
        ],
        self::GREATER_THAN => [
            'operatorName' => self::GREATER_THAN,
            'operatorSymbol' => '>',
            'operatorDescription' => self::GREATER_THAN,
        ],
        self::LESS_THAN => [
            'operatorName' => self::LESS_THAN,
            'operatorSymbol' => '<',
            'operatorDescription' => self::LESS_THAN,
        ],
        self::GREATER_THAN_OR_EQUAL_TO => [
            'operatorName' => self::GREATER_THAN_OR_EQUAL_TO,
            'operatorSymbol' => '>=',
            'operatorDescription' => self::GREATER_THAN_OR_EQUAL_TO,
        ],
        self::LESS_THAN_OR_EQUAL_TO => [
            'operatorName' => self::LESS_THAN_OR_EQUAL_TO,
            'operatorSymbol' => '<=',
            'operatorDescription' => self::LESS_THAN_OR_EQUAL_TO,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::OPERATORS as $operator) {
            $operatorEntity = new Operator();
            $operatorEntity->setOperatorName($operator['operatorName']);
            $operatorEntity->setOperatorSymbol($operator['operatorSymbol']);
            $operatorEntity->setOperatorDescription($operator['operatorDescription']);

            $manager->persist($operatorEntity);
            $this->addReference($operator['operatorName'], $operatorEntity);
        }

        $manager->flush();
    }
}
